<?php

namespace App\Livewire\Admin;

use App\Models\Pengguna;
use App\Models\Role;
use App\Models\WaliKelas;
use App\Models\WaliMurid;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Users extends Component
{
    // Properti utama untuk Pengguna
    public $name, $username, $email, $no_telpon, $password, $id_role;
    public $editingId = null;

    // Properti baru untuk Data Tambahan Dinamis
    public $nuptk;
    public $jabatan;
    public $hubungan;

    // --- Computed Property untuk mendapatkan nama role yang sedang dipilih ---
    // Diakses di Blade sebagai $this->selectedRoleName
    public function getSelectedRoleNameProperty()
    {
        if ($this->id_role) {
            $role = Role::find($this->id_role);
            return optional($role)->nama_role;
        }
        return null;
    }

    // --- Aturan Validasi ---
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pengguna', 'username')->ignore($this->editingId, 'id_pengguna')
            ],
            'email' => 'nullable|email|max:255',
            'no_telpon' => 'required|string|max:20',
            'id_role' => 'required|exists:role,id_role',
        ];

        if (!$this->editingId || $this->password) {
            $rules['password'] = 'required|string|min:6';
        }

        $roleName = $this->selectedRoleName;

        // Validasi untuk Wali Kelas / Guru BK
        if ($roleName == 'guru_bk' || $roleName == 'wali_kelas') {
            $waliKelasId = optional(Pengguna::find($this->editingId)->waliKelas)->id_walikelas;

            $rules['nuptk'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('wali_kelas', 'nuptk')->ignore($waliKelasId, 'id_walikelas')
            ];
            $rules['jabatan'] = 'required|string|max:100';
        } 
        // Validasi untuk Wali Murid (Orang Tua)
        elseif ($roleName == 'orang_tua') {
            $rules['hubungan'] = 'required|string|max:50';
        }

        return $rules;
    }
    
    // --- Render Component ---
    public function render()
    {
        return view('livewire.admin.users', [
            'users' => Pengguna::with('role', 'waliKelas', 'waliMurid')->get(),
            'roles' => Role::all(),
        ]);
    }

    // --- Reset Form ---
    public function resetForm()
    {
        $this->reset([
            'editingId', 'name', 'username', 'email', 'no_telpon', 'id_role', 'password',
            'nuptk', 'jabatan', 'hubungan'
        ]);
    }

    // --- Save / Update ---
    public function save()
    {
        $this->validate();

        // Data utama Pengguna
        $data = [
            'id_role' => $this->id_role,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'no_telpon' => $this->no_telpon,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $roleName = $this->selectedRoleName;

        // Simpan / Perbarui Pengguna
        if ($this->editingId) {
            $pengguna = Pengguna::where('id_pengguna', $this->editingId)->first();
            $pengguna->update($data);
            $message = 'Pengguna berhasil diperbarui!';
        } else {
            $pengguna = Pengguna::create($data);
            $message = 'Pengguna berhasil ditambahkan!';
        }

        // --- Logic Simpan Data Relasional ---
        if ($roleName == 'guru_bk' || $roleName == 'wali_kelas') {
            WaliKelas::updateOrCreate(
                ['id_pengguna' => $pengguna->id_pengguna],
                ['nuptk' => $this->nuptk, 'jabatan' => $this->jabatan]
            );
            $pengguna->waliMurid()->delete(); 
        } elseif ($roleName == 'orang_tua') {
            WaliMurid::updateOrCreate(
                ['id_pengguna' => $pengguna->id_pengguna],
                ['hubungan' => $this->hubungan]
            );
            $pengguna->waliKelas()->delete(); 
        } else {
             $pengguna->waliKelas()->delete();
             $pengguna->waliMurid()->delete();
        }

        $this->resetForm();
        session()->flash('success', $message);
    }

    // --- Edit User ---
    public function editUser($id)
    {
        $u = Pengguna::with(['waliKelas', 'waliMurid'])->findOrFail($id);

        $this->editingId = $id;
        $this->name = $u->name;
        $this->username = $u->username;
        $this->email = $u->email;
        $this->no_telpon = $u->no_telpon;
        $this->id_role = $u->id_role;
        $this->password = ''; 
        
        $this->nuptk = null;
        $this->jabatan = null;
        $this->hubungan = null;

        // Load data relasional
        if ($u->waliKelas) {
            $this->nuptk = $u->waliKelas->nuptk;
            $this->jabatan = $u->waliKelas->jabatan;
        } elseif ($u->waliMurid) {
            $this->hubungan = $u->waliMurid->hubungan;
        }
    }

    // --- Delete User ---
    public function deleteUser($id)
    {
        $user = Pengguna::where('id_pengguna', $id)->first();
        
        $user->waliKelas()->delete();
        $user->waliMurid()->delete();
        
        $user->delete();
        session()->flash('success', 'Pengguna berhasil dihapus');
    }
}