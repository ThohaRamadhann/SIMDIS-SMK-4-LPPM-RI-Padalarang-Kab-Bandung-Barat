<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KelasExport implements
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

        return Kelas::with('waliKelas.pengguna')->get();
    }

    public function headings(): array
    {
        return [
            'nama_kelas',
            'tingkat',           // X / XI / XII
            'jurusan',
            'tahun_ajaran',
            'username_walikelas', // opsional, FK via username
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama_kelas,
            $row->tingkat,
            $row->jurusan,
            $row->tahun_ajaran,
            optional(optional($row->waliKelas)->pengguna)->username,
        ];
    }

    public function title(): string
    {
        return 'Kelas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 12,
            'C' => 30,
            'D' => 15,
            'E' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
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

        $sheet->setCellValue('A2', '* tingkat: X / XI / XII | jurusan: Perhotelan / Rekayasa Perangkat Lunak / Teknik Jaringan Komputer / Teknik Bisnis Sepeda Motor');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A2:E2');

        return [];
    }
}