<?php

namespace App\Livewire\JenisPelanggaran;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JenisPelanggaran;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    // ── Filter & Paginasi ──────────────────────────────────────
    public string $search        = '';
    public string $filterTingkat = '';
    public int    $perPage       = 10;
    public bool   $showTrash     = false;

    // ── Form fields ────────────────────────────────────────────
    public string $nama_pelanggaran    = '';
    public string $tingkat_pelanggaran = '';

    // ── State modal ────────────────────────────────────────────
    public $editId             = null;
    public bool $showModal     = false;
    public bool $confirmDelete = false;
    public $deleteId           = null;

    // ── State hapus permanen ───────────────────────────────────
    public bool $confirmForceDelete = false;
    public $forceDeleteId           = null;

    // Reset halaman saat filter berubah
    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingFilterTingkat(): void { $this->resetPage(); }
    public function updatingPerPage(): void       { $this->resetPage(); }
    public function updatingShowTrash(): void     { $this->resetPage(); }

    // ── Validasi ───────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'nama_pelanggaran' => [
                'required',
                'string',
                'max:150',
                Rule::unique('jenis_pelanggaran', 'nama_pelanggaran')
                    ->ignore($this->editId, 'id_jenispelanggaran')
                    ->whereNull('deleted_at'),
            ],
            'tingkat_pelanggaran' => 'required|in:Ringan,Sedang,Berat',
        ];
    }

    protected $messages = [
        'nama_pelanggaran.required'    => 'Nama pelanggaran wajib diisi.',
        'nama_pelanggaran.unique'      => 'Nama pelanggaran ini sudah ada, gunakan nama lain.',
        'tingkat_pelanggaran.required' => 'Tingkat pelanggaran wajib dipilih.',
        'tingkat_pelanggaran.in'       => 'Tingkat tidak valid.',
    ];

    // ── Toggle Trash ───────────────────────────────────────────
    public function toggleTrash(): void
    {
        $this->showTrash = !$this->showTrash;
        $this->resetPage();
    }

    // ── Open modal form ────────────────────────────────────────
    public function openCreate(): void
    {
        $this->reset(['nama_pelanggaran', 'tingkat_pelanggaran', 'editId']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $item = JenisPelanggaran::findOrFail($id);
        $this->editId              = $id;
        $this->nama_pelanggaran    = $item->nama_pelanggaran;
        $this->tingkat_pelanggaran = $item->tingkat_pelanggaran;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    // ── Simpan (create / update) ───────────────────────────────
    public function save(): void
    {
        $this->validate();

        JenisPelanggaran::updateOrCreate(
            ['id_jenispelanggaran' => $this->editId],
            [
                'nama_pelanggaran'    => $this->nama_pelanggaran,
                'tingkat_pelanggaran' => $this->tingkat_pelanggaran,
            ]
        );

        $this->showModal = false;
        session()->flash('success', $this->editId
            ? 'Jenis pelanggaran berhasil diperbarui.'
            : 'Jenis pelanggaran berhasil ditambahkan.'
        );
    }

    // ── Soft Delete ────────────────────────────────────────────
    public function confirmDeleteItem(int $id): void
    {
        $this->deleteId      = $id;
        $this->confirmDelete = true;
    }

    public function deleteItem(): void
    {
        JenisPelanggaran::findOrFail($this->deleteId)->delete(); // soft delete
        $this->confirmDelete = false;
        $this->deleteId      = null;
        session()->flash('success', 'Jenis pelanggaran dipindahkan ke tong sampah.');
    }

    // ── Restore dari trash ─────────────────────────────────────
    public function restoreItem(int $id): void
    {
        JenisPelanggaran::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Jenis pelanggaran berhasil dipulihkan.');
    }

    // ── Hapus permanen ─────────────────────────────────────────
    public function confirmForceDeleteItem(int $id): void
    {
        $this->forceDeleteId      = $id;
        $this->confirmForceDelete = true;
    }

    public function forceDeleteItem(): void
    {
        JenisPelanggaran::withTrashed()->findOrFail($this->forceDeleteId)->forceDelete();
        $this->confirmForceDelete = false;
        $this->forceDeleteId      = null;
        session()->flash('success', 'Jenis pelanggaran dihapus permanen.');
    }

    // ── Tutup semua modal ──────────────────────────────────────
    public function closeModal(): void
    {
        $this->showModal          = false;
        $this->confirmDelete      = false;
        $this->confirmForceDelete = false;
        $this->resetErrorBag();
    }

    // ── Render ─────────────────────────────────────────────────
    public function render()
    {
        $trashCount = JenisPelanggaran::onlyTrashed()->count();

        $data = JenisPelanggaran::query()
            ->when($this->showTrash,
                fn ($q) => $q->onlyTrashed(),
                fn ($q) => $q
                    ->when($this->search, fn ($q) =>
                        $q->where('nama_pelanggaran', 'like', '%' . $this->search . '%')
                    )
                    ->when($this->filterTingkat, fn ($q) =>
                        $q->where('tingkat_pelanggaran', $this->filterTingkat)
                    )
            )
            ->orderBy('tingkat_pelanggaran')
            ->orderBy('nama_pelanggaran')
            ->paginate($this->perPage);

        return view('livewire.jenispelanggaran.index', compact('data', 'trashCount'));
    }
}