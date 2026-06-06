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
            'pengguna'   => new PenggunaExport($isTemplate),
            'wali_kelas' => new WaliKelasExport($isTemplate),
            'wali_siswa' => new WaliSiswaExport($isTemplate),
            'kelas'      => new KelasExport($isTemplate),
            'siswa'      => new SiswaExport($isTemplate),
            default      => null,
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