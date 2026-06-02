<?php

namespace App\Livewire\Admin;

use App\Models\Pengguna;
use App\Models\Role;
use App\Models\WaliKelas;
use App\Models\WaliMurid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $name, $username, $email, $no_telpon, $password, $id_role;
    public $editingId = null;
    public $nuptk, $jabatan, $hubungan;

    // ── Search, filter, sort, pagination ──
    public $search     = '';
    public $filterRole = '';
    public $sortBy     = 'terbaru';
    public $perPage    = 10;

    // ── Trash / soft delete ──
    public $showTrash = false;

    protected $queryString = [
        'search'     => ['except' => ''],
        'filterRole' => ['except' => ''],
        'sortBy'     => ['except' => 'terbaru'],
        'perPage'    => ['except' => 10],
    ];

    public function updatingSearch()     { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }
    public function updatingSortBy()     { $this->resetPage(); }
    public function updatingPerPage()    { $this->resetPage(); }
    public function updatingShowTrash()  { $this->resetPage(); }

    // ── Computed: nama role yang dipilih ──
    public function getSelectedRoleNameProperty()
    {
        if ($this->id_role) {
            return optional(Role::find($this->id_role))->nama_role;
        }
        return null;
    }

    // ── Validasi ──
    protected function rules()
    {
        $rules = [
            'name'      => 'required|string|max:255',
            'username'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('pengguna', 'username')
                    ->ignore($this->editingId, 'id_pengguna'),
            ],
            'email'     => 'nullable|email|max:255',
            'no_telpon' => 'required|string|max:20',
            'id_role'   => 'required|exists:role,id_role',
        ];

        if (!$this->editingId || $this->password) {
            $rules['password'] = 'required|string|min:6';
        }

        $roleName = $this->selectedRoleName;

        // Hanya wali_kelas yang butuh NUPTK & jabatan
        // guru_bk cukup data pengguna saja (seperti admin)
        if ($roleName === 'wali_kelas') {
            $waliKelasId = null;
            if ($this->editingId) {
                $pengguna    = Pengguna::with('waliKelas')->find($this->editingId);
                $waliKelasId = optional($pengguna?->waliKelas)->id_walikelas;
            }
            $rules['nuptk']   = [
                'required',
                'string',
                'max:50',
                Rule::unique('wali_kelas', 'nuptk')->ignore($waliKelasId, 'id_walikelas'),
            ];
            $rules['jabatan'] = 'required|string|max:100';
        }

        if ($roleName === 'orang_tua') {
            $rules['hubungan'] = 'required|string|max:50';
        }

        return $rules;
    }

    // ── Render ──
    public function render()
    {
        $query = Pengguna::with('role', 'waliKelas', 'waliMurid');

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterRole) {
            $query->where('id_role', $this->filterRole);
        }

        match ($this->sortBy) {
            'az'    => $query->orderBy('name', 'asc'),
            'za'    => $query->orderBy('name', 'desc'),
            default => $query->orderBy('id_pengguna', 'desc'),
        };

        $users = $query->paginate($this->perPage);

        return view('livewire.admin.users', [
            'users'      => $users,
            'roles'      => Role::all(),
            'trashCount' => Pengguna::onlyTrashed()->count(),
            'startNo'    => $users->firstItem(),
        ]);
    }

    // ── Reset form ──
    public function resetForm()
    {
        $this->reset([
            'editingId',
            'name',
            'username',
            'email',
            'no_telpon',
            'id_role',
            'password',
            'nuptk',
            'jabatan',
            'hubungan',
        ]);
    }

    // ── Simpan / Update ──
    public function save()
    {
        $this->validate();

        $data = [
            'id_role'   => $this->id_role,
            'name'      => $this->name,
            'username'  => $this->username,
            'email'     => $this->email,
            'no_telpon' => $this->no_telpon,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $roleName = $this->selectedRoleName;

        if ($this->editingId) {
            $pengguna = Pengguna::findOrFail($this->editingId);
            $pengguna->update($data);
            $message = 'Pengguna berhasil diperbarui!';
        } else {
            $pengguna = Pengguna::create($data);
            $message  = 'Pengguna berhasil ditambahkan!';
        }

        // Hanya wali_kelas yang masuk tabel wali_kelas
        // guru_bk, admin, dll → hanya di tabel pengguna
        if ($roleName === 'wali_kelas') {
            WaliKelas::updateOrCreate(
                ['id_pengguna' => $pengguna->id_pengguna],
                ['nuptk' => $this->nuptk, 'jabatan' => $this->jabatan]
            );
            $pengguna->waliMurid()->delete();
        } elseif ($roleName === 'orang_tua') {
            WaliMurid::updateOrCreate(
                ['id_pengguna' => $pengguna->id_pengguna],
                ['hubungan' => $this->hubungan]
            );
            $pengguna->waliKelas()->delete();
        } else {
            // admin, guru_bk, dan role lain:
            // bersihkan relasi jika sebelumnya pernah punya
            $pengguna->waliKelas()->delete();
            $pengguna->waliMurid()->delete();
        }

        $this->resetForm();
        session()->flash('success', $message);
    }

    // ── Edit ──
    public function editUser($id)
    {
        $u = Pengguna::with(['waliKelas', 'waliMurid'])->findOrFail($id);

        $this->editingId = $id;
        $this->name      = $u->name;
        $this->username  = $u->username;
        $this->email     = $u->email;
        $this->no_telpon = $u->no_telpon;
        $this->id_role   = $u->id_role;
        $this->password  = '';
        $this->nuptk     = null;
        $this->jabatan   = null;
        $this->hubungan  = null;

        if ($u->waliKelas) {
            $this->nuptk   = $u->waliKelas->nuptk;
            $this->jabatan = $u->waliKelas->jabatan;
        } elseif ($u->waliMurid) {
            $this->hubungan = $u->waliMurid->hubungan;
        }
    }

    // ── Soft Delete ──
    public function deleteUser($id)
    {
        $user = Pengguna::findOrFail($id);
        $user->waliKelas()->delete();
        $user->waliMurid()->delete();
        $user->delete();
        $this->resetForm();
        session()->flash('success', 'Pengguna dipindahkan ke tong sampah.');
    }

    // ── Restore dari trash ──
    public function restoreUser($id)
    {
        Pengguna::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Pengguna berhasil dipulihkan.');
    }

    // ── Hapus permanen ──
    public function forceDeleteUser($id)
    {
        $user = Pengguna::onlyTrashed()->findOrFail($id);
        $user->waliKelas()->forceDelete();
        $user->waliMurid()->forceDelete();
        $user->forceDelete();
        session()->flash('success', 'Pengguna dihapus permanen.');
    }

    // ── Kosongkan seluruh trash ──
    public function emptyTrash()
    {
        $trashed = Pengguna::onlyTrashed()->get();
        foreach ($trashed as $user) {
            $user->waliKelas()->forceDelete();
            $user->waliMurid()->forceDelete();
            $user->forceDelete();
        }
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    #[On('refresh')]
    public function refreshData(): void {}
}