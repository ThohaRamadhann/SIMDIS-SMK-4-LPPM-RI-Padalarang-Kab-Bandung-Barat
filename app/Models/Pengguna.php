<?php
// app/Models/Pengguna.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;   // ← tambah
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait; // ← tambah
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable implements CanResetPassword
{
    use SoftDeletes, Notifiable, CanResetPasswordTrait; // ← tambah Notifiable & trait

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'id_role',
        'name',
        'username',
        'email',
        'no_telpon',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Tambah casts agar password_reset_tokens bisa match
    protected $casts = [
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function waliMurid()
    {
        return $this->hasOne(WaliMurid::class, 'id_pengguna');
    }

    public function waliKelas()
    {
        return $this->hasOne(WaliKelas::class, 'id_pengguna');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_pengguna');
    }
}