<?php

namespace App\Exports;

use App\Models\Kelas;
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

class KelasExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected bool $templateOnly;
    protected ?string $filterTingkat;
    protected ?string $filterJurusan;
    protected ?string $filterTahun;
    protected ?string $filterWali;
    protected ?string $search;
    protected int $totalRows = 0;

    public function __construct(
        bool $templateOnly = false,
        ?string $filterTingkat = null,
        ?string $filterJurusan = null,
        ?string $filterTahun   = null,
        ?string $filterWali    = null,
        ?string $search        = null,
    ) {
        $this->templateOnly  = $templateOnly;
        $this->filterTingkat = $filterTingkat;
        $this->filterJurusan = $filterJurusan;
        $this->filterTahun   = $filterTahun;
        $this->filterWali    = $filterWali;
        $this->search        = $search;
    }

    public function collection()
    {
        if ($this->templateOnly) return collect([]);

        $query = Kelas::with('waliKelas.pengguna');

        if ($this->filterTingkat) {
            $query->where('tingkat', $this->filterTingkat);
        }

        if ($this->filterJurusan) {
            $query->where('jurusan', $this->filterJurusan);
        }

        if ($this->filterTahun) {
            $query->where('tahun_ajaran', $this->filterTahun);
        }

        if ($this->filterWali === 'ada') {
            $query->whereNotNull('id_walikelas');
        } elseif ($this->filterWali === 'kosong') {
            $query->whereNull('id_walikelas');
        }

        if ($this->search) {
            $query->where('nama_kelas', 'like', '%' . $this->search . '%');
        }

        $data = $query->get();
        $this->totalRows = $data->count();

        return $data;
    }

    public function headings(): array
    {
        return ['nama_kelas', 'tingkat', 'jurusan', 'tahun_ajaran', 'username_walikelas'];
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

    public function title(): string { return 'Kelas'; }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 12, 'C' => 30, 'D' => 15, 'E' => 25];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
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
                $row = $this->totalRows + 2;

                $event->sheet->setCellValue(
                    'A' . $row,
                    '* tingkat: X / XI / XII | jurusan: Akomodasi Perhotelan / Rekayasa Perangkat Lunak / Teknik Komputer Jaringan / Teknik Bisnis Sepeda Motor'
                );

                $event->sheet->getStyle('A' . $row)->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['rgb' => '888888'], 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $event->sheet->mergeCells('A' . $row . ':E' . $row);
            },
        ];
    }
}