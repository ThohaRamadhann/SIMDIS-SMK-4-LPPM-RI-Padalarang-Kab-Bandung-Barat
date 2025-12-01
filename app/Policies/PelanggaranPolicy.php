<?php

namespace App\Policies;

use App\Models\Pelanggaran;
use App\Models\Pengguna;
use Illuminate\Auth\Access\HandlesAuthorization;

class PelanggaranPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any Pelanggaran.
     */
    public function viewAny(Pengguna $user): bool
    {
        return in_array($user->role->nama_role, ['admin', 'guru_bk', 'wali_kelas', 'orang_tua']);
    }

    /**
     * Determine whether the user can view a specific Pelanggaran.
     */
    public function view(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        $role = $user->role->nama_role;

        if (in_array($role, ['admin', 'guru_bk'])) {
            return true;
        }

        if ($role === 'wali_kelas') {
            return $pelanggaran->id_walikelas === $user->waliKelas->id_walikelas;
        }

        if ($role === 'orang_tua') {
            return $pelanggaran->siswa->id_walimurid === $user->waliMurid->id_walimurid;
        }

        return false;
    }

    /**
     * Determine whether the user can create Pelanggaran.
     */
    public function create(Pengguna $user): bool
    {
        $role = $user->role->nama_role;
        return in_array($role, ['admin','guru_bk', 'wali_kelas']);
    }

    /**
     * Determine whether the user can update the Pelanggaran.
     */
    public function update(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        $role = $user->role->nama_role;

        if ($role === 'admin') return true;
        if ($role === 'guru_bk') return true;
        if ($role === 'wali_kelas') return $pelanggaran->status_pembinaan === null;

        return false;
    }

    /**
     * Determine whether the user can delete the Pelanggaran.
     */
    public function delete(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        $role = $user->role->nama_role;
        return in_array($role, ['admin', 'guru_bk']);
    }

    /**
     * Determine whether the user can restore the Pelanggaran.
     */
    public function restore(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        return $user->role->nama_role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the Pelanggaran.
     */
    public function forceDelete(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        return $user->role->nama_role === 'admin';
    }
}
