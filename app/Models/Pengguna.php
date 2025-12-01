<?php

namespace App\Models;

use App\Models\Role;
use App\Models\WaliKelas;
use App\Models\WaliMurid;
use App\Models\Notifikasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
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
