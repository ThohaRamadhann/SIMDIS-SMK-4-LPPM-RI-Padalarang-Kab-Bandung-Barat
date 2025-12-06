<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class Users extends Component
{
    public $name, $username, $email, $no_telpon, $password, $id_role;
    public $editingId = null;

    public function render()
    {
        return view('livewire.admin.users', [
            'users' => Pengguna::with('role')->get(),
            'roles' => Role::all(),
        ]);
    }

    public function resetForm()
    {
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->no_telpon = '';
        $this->password = '';
        $this->id_role = '';
        $this->editingId = null;
    }

    public function save()
    {
        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'no_telpon' => $this->no_telpon,
            'id_role' => $this->id_role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            Pengguna::where('id_pengguna', $this->editingId)->update($data);
            session()->flash('success', 'Pengguna berhasil diperbarui');
        } else {
            Pengguna::create($data);
            session()->flash('success', 'Pengguna berhasil ditambahkan');
        }

        $this->resetForm();
    }

    public function editUser($id)
    {
        $u = Pengguna::findOrFail($id);

        $this->editingId = $id;
        $this->name = $u->name;
        $this->username = $u->username;
        $this->email = $u->email;
        $this->no_telpon = $u->no_telpon;
        $this->id_role = $u->id_role;
    }

    public function deleteUser($id)
    {
        Pengguna::where('id_pengguna', $id)->delete();
        session()->flash('success', 'Pengguna berhasil dihapus');
    }
}
