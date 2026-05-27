<?php

namespace App\Exports;

use App\Models\WaliKelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WaliKelasExport implements
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

        return WaliKelas::with('pengguna')->get();
    }

    public function headings(): array
    {
        return [
            'username_pengguna', // FK via username
            'nuptk',
            'jabatan',           // walikelas / guru_bk
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->pengguna)->username,
            $row->nuptk,
            $row->jabatan,
        ];
    }

    public function title(): string
    {
        return 'Wali Kelas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 22,
            'C' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
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

        $sheet->setCellValue('A2', '* username_pengguna harus sudah terdaftar. jabatan: walikelas / guru_bk');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A2:C2');

        return [];
    }
}