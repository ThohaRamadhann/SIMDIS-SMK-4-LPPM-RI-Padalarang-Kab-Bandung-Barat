<?php

namespace App\Livewire\Admin\WaliKelas;

use App\Models\Pengguna;
use App\Models\WaliKelas;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $id_walikelas;
    public $id_pengguna = '';
    public $nuptk       = '';
    public $jabatan     = '';
    public $isEdit      = false;

    // ── Search, sort, pagination, trash ──
    public $search    = '';
    public $sortBy    = 'terbaru';
    public $perPage   = 10;
    public $showTrash = false;

    protected $queryString = [
        'search'  => ['except' => ''],
        'sortBy'  => ['except' => 'terbaru'],
        'perPage' => ['except' => 10],
    ];

    public function mount(): void
    {
        $this->showTrash   = false;
        $this->search      = '';
        $this->sortBy      = 'terbaru';
        $this->perPage     = 10;
        $this->isEdit      = false;
        $this->id_pengguna = '';
        $this->nuptk       = '';
        $this->jabatan     = '';
    }

    public function updatingSearch()    { $this->resetPage(); }
    public function updatingSortBy()    { $this->resetPage(); }
    public function updatingPerPage()   { $this->resetPage(); }
    public function updatingShowTrash() { $this->resetPage(); }

    protected $listeners = ['updatedIdPengguna'];

    public function updatedIdPengguna($value)
    {
        if ($value) {
            $wk            = WaliKelas::where('id_pengguna', $value)->first();
            $this->nuptk   = $wk?->nuptk  ?? '';
            $this->jabatan = $wk?->jabatan ?? '';
        } else {
            $this->nuptk = $this->jabatan = '';
        }
    }

    protected function rules(): array
    {
        $nuPtkRules = [
            'nullable',
            'digits:16',
        ];

        if ($this->isEdit) {
            $nuPtkRules[] = Rule::unique('wali_kelas', 'nuptk')
                ->ignore($this->id_walikelas, 'id_walikelas');
        } else {
            $nuPtkRules[] = Rule::unique('wali_kelas', 'nuptk');
        }

        return [
            'id_pengguna' => 'required',
            'nuptk'       => $nuPtkRules,
            'jabatan'     => 'nullable|string|max:100',
        ];
    }

    protected function messages(): array
    {
        return [
            'id_pengguna.required' => 'Pengguna wajib dipilih.',
            'nuptk.digits'         => 'NUPTK harus berupa angka 16 digit.',
            'nuptk.unique'         => 'NUPTK sudah terdaftar pada pengguna lain.',
            'jabatan.max'          => 'Jabatan maksimal 100 karakter.',
        ];
    }

    public function resetForm()
    {
        $this->id_walikelas = null;
        $this->id_pengguna  = '';
        $this->nuptk        = '';
        $this->jabatan      = '';
        $this->isEdit       = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        $existing = WaliKelas::where('id_pengguna', $this->id_pengguna)->first();

        if ($existing) {
            $existing->update([
                'nuptk'   => $this->nuptk ?: null,
                'jabatan' => $this->jabatan,
            ]);
        } else {
            WaliKelas::create([
                'id_pengguna' => $this->id_pengguna,
                'nuptk'       => $this->nuptk ?: null,
                'jabatan'     => $this->jabatan,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil disimpan.');
    }

    public function edit($id)
    {
        $data = WaliKelas::findOrFail($id);

        $this->id_walikelas = $data->id_walikelas;
        $this->id_pengguna  = $data->id_pengguna;
        $this->nuptk        = $data->nuptk ?? '';
        $this->jabatan      = $data->jabatan ?? '';
        $this->isEdit       = true;
        $this->resetErrorBag();
    }

    public function update()
    {
        $this->validate();

        WaliKelas::findOrFail($this->id_walikelas)->update([
            'id_pengguna' => $this->id_pengguna,
            'nuptk'       => $this->nuptk ?: null,
            'jabatan'     => $this->jabatan,
        ]);

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil diperbarui.');
    }

    public function hapus($id)
    {
        WaliKelas::findOrFail($id)->delete();
        session()->flash('success', 'Wali Kelas dipindahkan ke tong sampah.');
    }

    public function restore($id)
    {
        WaliKelas::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Wali Kelas berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        WaliKelas::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Wali Kelas dihapus permanen.');
    }

    public function emptyTrash()
    {
        WaliKelas::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    public function render()
    {
        $query = WaliKelas::with('pengguna');

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $keyword = $this->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nuptk', 'like', "%{$keyword}%")
                  ->orWhereHas('pengguna', fn($r) =>
                      $r->where('name', 'like', "%{$keyword}%")
                  );
            });
        }

        match ($this->sortBy) {
            'az'    => $query->join('pengguna', 'wali_kelas.id_pengguna', '=', 'pengguna.id_pengguna')
                             ->orderBy('pengguna.name', 'asc')
                             ->select('wali_kelas.*'),
            'za'    => $query->join('pengguna', 'wali_kelas.id_pengguna', '=', 'pengguna.id_pengguna')
                             ->orderBy('pengguna.name', 'desc')
                             ->select('wali_kelas.*'),
            default => $query->orderBy('wali_kelas.id_walikelas', 'desc'),
        };

        $waliKelasPengguna = Pengguna::whereHas('role', function ($q) {
            $q->where('nama_role', 'wali_kelas');
        })->orderBy('name')->get();

        if ($this->isEdit && $this->id_pengguna) {
            $currentPengguna   = Pengguna::find($this->id_pengguna);
            $waliKelasPengguna = $waliKelasPengguna
                ->push($currentPengguna)
                ->unique('id_pengguna')
                ->sortBy('name');
        }

        return view('livewire.admin.walikelas.index', [
            'dataWK'       => $query->paginate($this->perPage),
            'pengguna'     => $waliKelasPengguna,
            'trashCount'   => WaliKelas::onlyTrashed()->count(),
            'isEdit'       => $this->isEdit,
            'id_walikelas' => $this->id_walikelas,
            'id_pengguna'  => $this->id_pengguna,
            'nuptk'        => $this->nuptk,
            'jabatan'      => $this->jabatan,
        ]);
    }

    #[On('refresh')]
    public function refreshData(): void {}
}