<?php

namespace App\Policies;

use App\Models\Pelanggaran;
use App\Models\Pengguna;
use Illuminate\Auth\Access\HandlesAuthorization;

class PelanggaranPolicy
{
    use HandlesAuthorization;

    /**
     * Siapa yang boleh melihat daftar pelanggaran.
     */
    public function viewAny(Pengguna $user): bool
    {
        return in_array($user->role->nama_role, [
            'admin', 'guru_bk', 'wali_kelas', 'wali_siswa', // wali_siswa = wali_siswa lama
        ]);
    }

    /**
     * Siapa yang boleh melihat satu pelanggaran.
     */
    public function view(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        $role = $user->role->nama_role;

        if (in_array($role, ['admin', 'guru_bk'])) {
            return true;
        }

        if ($role === 'wali_kelas') {
            return $pelanggaran->id_walikelas === optional($user->waliKelas)->id_walikelas;
        }

        if ($role === 'wali_siswa') {
            // Pastikan pelanggaran ini milik anak dari wali siswa yang login
            // id_walisiswa menggantikan id_walimurid
            return optional($pelanggaran->siswa)->id_walisiswa
                === optional($user->waliSiswa)->id_walisiswa;
        }

        return false;
    }

    /**
     * Siapa yang boleh membuat pelanggaran baru.
     */
    public function create(Pengguna $user): bool
    {
        return in_array($user->role->nama_role, ['admin', 'guru_bk', 'wali_kelas']);
    }

    /**
     * Siapa yang boleh mengubah pelanggaran.
     */
    public function update(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        $role = $user->role->nama_role;

        if ($role === 'admin')    return true;
        if ($role === 'guru_bk')  return true;
        if ($role === 'wali_kelas') return $pelanggaran->status_pembinaan === null;

        // wali_siswa tidak boleh edit
        return false;
    }

    /**
     * Siapa yang boleh menghapus pelanggaran.
     */
    public function delete(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        return in_array($user->role->nama_role, ['admin', 'guru_bk']);
    }

    /**
     * Siapa yang boleh memulihkan pelanggaran dari trash.
     */
    public function restore(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        return $user->role->nama_role === 'admin';
    }

    /**
     * Siapa yang boleh menghapus permanen.
     */
    public function forceDelete(Pengguna $user, Pelanggaran $pelanggaran): bool
    {
        return $user->role->nama_role === 'admin';
    }
}