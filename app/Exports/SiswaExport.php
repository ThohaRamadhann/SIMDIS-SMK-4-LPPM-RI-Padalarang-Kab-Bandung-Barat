<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected bool $templateOnly;

    public function __construct(bool $templateOnly = false)
    {
        $this->templateOnly = $templateOnly;
    }

    public function collection()
    {
        if ($this->templateOnly) {
            return collect([]);
        }

        return Siswa::with(['waliMurid.pengguna', 'kelas'])->get();
    }

    public function headings(): array
    {
        return [
            'nama',
            'nis',
            'username_walimurid', // FK via username pengguna
            'nama_kelas',
            'tahun_ajaran',
            'status',             // aktif / tidak_aktif / lulus
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama,
            $row->nis,
            optional(optional($row->waliMurid)->pengguna)->username,
            optional($row->kelas)->nama_kelas,
            optional($row->kelas)->tahun_ajaran,
            $row->status,
        ];
    }

    public function title(): string
    {
        return 'Siswa';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 25,
            'D' => 20,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D2D6B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->setCellValue('A2', '* status: aktif / tidak_aktif / lulus | username_walimurid & nama_kelas harus sudah terdaftar');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A2:F2');

        return [];
    }
}