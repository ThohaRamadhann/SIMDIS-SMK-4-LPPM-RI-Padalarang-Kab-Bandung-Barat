<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected bool $templateOnly;
    protected ?string $filterTahunAjaran;
    protected int $totalRows = 0;

    public function __construct(bool $templateOnly = false, ?string $filterTahunAjaran = null)
    {
        $this->templateOnly      = $templateOnly;
        $this->filterTahunAjaran = $filterTahunAjaran;
    }

    public function collection()
    {
        if ($this->templateOnly) return collect([]);

        $query = Siswa::with(['waliMurid.pengguna', 'kelas']);

        if ($this->filterTahunAjaran) {
            $query->whereHas('kelas', fn($q) =>
                $q->where('tahun_ajaran', $this->filterTahunAjaran)
            );
        }

        $data = $query->get();
        $this->totalRows = $data->count();

        return $data;
    }

    public function headings(): array
    {
        return [
            'nama',
            'nis',
            'username_walimurid',
            'nama_kelas',
            'tahun_ajaran',
            'status',
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
        // Style header baris 1
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

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Baris keterangan ditulis SETELAH data
                // Baris 1 = heading, baris 2..N+1 = data, baris N+2 = keterangan
                $keteranganRow = $this->totalRows + 2;

                $event->sheet->setCellValue(
                    'A' . $keteranganRow,
                    '* status: aktif / nonaktif | username_walimurid & nama_kelas harus sudah terdaftar'
                );

                $event->sheet->getStyle('A' . $keteranganRow)->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $event->sheet->mergeCells('A' . $keteranganRow . ':F' . $keteranganRow);
            },
        ];
    }
}