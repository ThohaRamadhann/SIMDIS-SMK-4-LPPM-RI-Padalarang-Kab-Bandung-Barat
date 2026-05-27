<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Imports\PenggunaImport;
use App\Imports\WaliKelasImport;
use App\Imports\WaliMuridImport;
use App\Imports\KelasImport;
use App\Imports\SiswaImport;

class ImportData extends Component
{
    use WithFileUploads;

    public string $type = '';

    public        $file          = null;
    public bool   $importing     = false;
    public bool   $done          = false;
    public bool   $previewing    = false;  // ← mode preview
    public int    $imported      = 0;
    public array  $importErrors  = [];
    public array  $previewRows   = [];     // ← data preview
    public array  $previewErrors = [];     // ← error per baris saat preview

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'file.required' => 'Pilih file terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ];
    }

    public function updatedFile()
    {
        $this->validate(['file' => 'file|mimes:xlsx,xls,csv|max:5120']);
        $this->done          = false;
        $this->previewing    = false;
        $this->previewRows   = [];
        $this->previewErrors = [];
        $this->importErrors  = [];
    }

    // ── PREVIEW: baca file, tampilkan tabel + validasi per baris ──
    public function preview()
    {
        $this->validate();

        // Baca file pakai anonymous class ToCollection + WithHeadingRow
        $rows = Excel::toCollection(new class implements ToCollection, WithHeadingRow {
            public Collection $rows;
            public function collection(Collection $rows)
            {
                $this->rows = $rows;
            }
        }, $this->file->getRealPath())->first() ?? collect();

        $this->previewRows   = $rows->toArray();
        $this->previewErrors = [];

        // Validasi per baris sesuai type
        foreach ($rows as $index => $row) {
            $rowNum    = $index + 2; // baris 1 = header
            $rowErrors = $this->validateRow($row->toArray(), $rowNum);
            if (!empty($rowErrors)) {
                $this->previewErrors[$index] = $rowErrors;
            }
        }

        $this->previewing = true;
    }

    // ── Validasi per baris sesuai type ──
    private function validateRow(array $row, int $rowNum): array
    {
        $errors = [];

        $required = $this->getRequiredColumns();
        foreach ($required as $col) {
            if (empty($row[$col])) {
                $errors[] = "Kolom '{$col}' wajib diisi.";
            }
        }

        return $errors;
    }

    private function getRequiredColumns(): array
    {
        return match ($this->type) {
            'pengguna'   => ['name', 'username', 'password', 'role'], // ← name bukan nama
            'wali_kelas' => ['username_pengguna', 'jabatan'],
            'wali_murid' => ['username_pengguna', 'hubungan'],
            'kelas'      => ['nama_kelas', 'tingkat', 'jurusan', 'tahun_ajaran'],
            'siswa'      => ['nama', 'nis', 'username_walimurid', 'nama_kelas', 'tahun_ajaran'],
            default      => [],
        };
    }

    // ── IMPORT: jalankan import sesungguhnya ──
    public function import()
    {
        $this->importing    = true;
        $this->importErrors = [];
        $this->imported     = 0;

        $importer = $this->resolveImporter();

        if (!$importer) {
            $this->addError('file', 'Tipe import tidak valid.');
            $this->importing = false;
            return;
        }

        try {
            Excel::import($importer, $this->file->getRealPath());

            $skipFailures = collect($importer->failures())
                ->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()))
                ->toArray();

            $this->importErrors = array_merge($importer->errors, $skipFailures);
            $this->imported     = $importer->imported;
            $this->done         = true;
            $this->previewing   = false;
            $this->dispatch('refresh');
        } catch (\Exception $e) {
            $this->importErrors[] = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->done           = true;
            $this->previewing     = false;
        }

        $this->file      = null;
        $this->importing = false;
    }

    private function resolveImporter(): ?object
    {
        return match ($this->type) {
            'pengguna'   => new PenggunaImport(),
            'wali_kelas' => new WaliKelasImport(),
            'wali_murid' => new WaliMuridImport(),
            'kelas'      => new KelasImport(),
            'siswa'      => new SiswaImport(),
            default      => null,
        };
    }

    public function reset_form()
    {
        $this->file          = null;
        $this->done          = false;
        $this->previewing    = false;
        $this->previewRows   = [];
        $this->previewErrors = [];
        $this->importErrors  = [];
        $this->imported      = 0;
    }

    public function render()
    {
        return view('livewire.admin.import-data');
    }
}
