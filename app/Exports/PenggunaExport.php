<?php

namespace App\Exports;

use App\Models\Pengguna;
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

class PenggunaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected bool $templateOnly;
    protected ?string $filterRole;
    protected ?string $search;
    protected int $totalRows = 0;

    public function __construct(
        bool $templateOnly = false,
        ?string $filterRole = null,
        ?string $search = null,
    ) {
        $this->templateOnly = $templateOnly;
        $this->filterRole   = $filterRole;
        $this->search       = $search;
    }

    public function collection()
    {
        if ($this->templateOnly) {
            return collect([]);
        }

        $query = Pengguna::with('role');

        if ($this->filterRole) {
            $query->where('id_role', $this->filterRole);
        }

        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
            );
        }

        $data = $query->get();
        $this->totalRows = $data->count();

        return $data;
    }

    public function headings(): array
    {
        return ['name', 'username', 'email', 'no_telpon', 'password', 'role'];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->username,
            $row->email,
            $row->no_telpon,
            '',
            optional($row->role)->nama_role,
        ];
    }

    public function title(): string { return 'Pengguna'; }

    public function columnWidths(): array
    {
        return ['A' => 25, 'B' => 20, 'C' => 30, 'D' => 18, 'E' => 20, 'F' => 18];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D2D6B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $keteranganRow = $this->totalRows + 2;

                $event->sheet->setCellValue(
                    'A' . $keteranganRow,
                    '* Isi kolom password untuk pengguna baru. role: admin / wali_kelas / guru_bk / wali_siswa'
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