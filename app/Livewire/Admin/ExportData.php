<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\PenggunaExport;
use App\Exports\WaliKelasExport;
use App\Exports\WaliSiswaExport;
use App\Exports\KelasExport;
use App\Exports\SiswaExport;

class ExportData extends Component
{
    public string $type = '';
    public string $mode = 'template';

    // ── Filter siswa ──
    public string $filterTahunAjaran = '';
    public string $filterKelas       = '';
    public string $filterStatus      = '';

    // ── Filter pengguna ──
    public string $filterRole = '';

    // ── Filter shared ──
    public string $search = '';

    public string $filterTingkat = '';
    public string $filterJurusan = '';
    public string $filterTahun   = '';
    public string $filterWali    = '';

    protected $listeners = [
        'filter-changed'        => 'syncFilters',
        'filter-pengguna-changed' => 'syncFiltersPengguna',
        'filter-kelas-changed'  => 'syncFiltersKelas',
    ];

    public function syncFiltersKelas(array $filters): void
    {
        $this->filterTingkat = $filters['filterTingkat'] ?? '';
        $this->filterKelas       = $filters['filterKelas']       ?? '';
        $this->filterJurusan = $filters['filterJurusan'] ?? '';
        $this->filterTahun   = $filters['filterTahun']   ?? '';
        $this->filterWali    = $filters['filterWali']    ?? '';
        $this->search        = $filters['search']        ?? '';
    }

    public function syncFiltersPengguna(array $filters): void
    {
        $this->filterRole = $filters['filterRole'] ?? '';
        $this->search     = $filters['search']     ?? '';
    }

    public function syncFilters(array $filters): void
    {
        $this->filterTahunAjaran = $filters['filterTahunAjaran'] ?? '';
        $this->filterKelas       = $filters['filterKelas']       ?? '';
        $this->filterStatus      = $filters['filterStatus']      ?? '';
        $this->search            = $filters['search']            ?? '';
    }

    public function exportAs(string $mode)
    {
        $this->mode = $mode;

        $exporter = $this->resolveExporter();

        if (!$exporter) {
            return;
        }

        return Excel::download($exporter, $this->buildFilename());
    }

    private function resolveExporter(): ?object
    {
        $isTemplate = $this->mode === 'template';

        return match ($this->type) {
            'pengguna'   => new PenggunaExport(
                templateOnly: $isTemplate,
                filterRole: $this->filterRole ?: null,
                search: $this->search     ?: null,
            ),
            'wali_kelas' => new WaliKelasExport($isTemplate),
            'wali_siswa' => new WaliSiswaExport($isTemplate),
            'kelas' => new KelasExport(
                templateOnly: $isTemplate,
                filterTingkat: $this->filterTingkat ?: null,
                filterJurusan: $this->filterJurusan ?: null,
                filterTahun: $this->filterTahun   ?: null,
                filterWali: $this->filterWali    ?: null,
                search: $this->search        ?: null,
            ),
            'siswa'      => new SiswaExport(
                templateOnly: $isTemplate,
                filterTahunAjaran: $this->filterTahunAjaran ?: null,
                filterKelas: $this->filterKelas       ?: null,
                filterStatus: $this->filterStatus      ?: null,
                search: $this->search            ?: null,
            ),
            default => null,
        };
    }

    private function buildFilename(): string
    {
        $label = match ($this->type) {
            'pengguna'   => 'pengguna',
            'wali_kelas' => 'wali-kelas',
            'wali_siswa' => 'wali-siswa',
            'kelas'      => 'kelas',
            'siswa'      => 'siswa',
            default      => 'data',
        };

        $suffix = $this->mode === 'template'
            ? 'template'
            : 'export-' . now()->format('Ymd-His');

        return "simdis-{$label}-{$suffix}.xlsx";
    }

    public function render()
    {
        return view('livewire.admin.export-data');
    }
}
