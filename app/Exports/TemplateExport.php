<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    public function __construct(
        private array  $headers,
        private array  $rows,
        private string $catatan = ''
    ) {}

    public function headings(): array
    {
        return $this->headers;
    }

    public function array(): array
    {
        $data = $this->rows;

        if ($this->catatan) {
            $data[] = []; // baris kosong pemisah
            $data[] = [$this->catatan];
        }

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headers));

        return [
            // Header row — background biru tua, teks putih, bold
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->headers as $i => $header) {
            $col          = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $widths[$col] = max(20, strlen($header) * 2 + 5);
        }
        return $widths;
    }
}