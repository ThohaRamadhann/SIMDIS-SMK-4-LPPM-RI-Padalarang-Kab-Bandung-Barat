<?php

namespace App\Exports;

use App\Models\WaliSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WaliSiswaExport implements
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

        return WaliSiswa::with('pengguna')->get();
    }

    public function headings(): array
    {
        return [
            'username_pengguna', // FK via username
            'hubungan',          // ayah / ibu / wali
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->pengguna)->username,
            $row->hubungan,
        ];
    }

    public function title(): string
    {
        return 'Wali Siswa';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
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

        $sheet->setCellValue('A2', '* username_pengguna harus sudah terdaftar. hubungan: ayah / ibu / wali');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A2:B2');

        return [];
    }
}