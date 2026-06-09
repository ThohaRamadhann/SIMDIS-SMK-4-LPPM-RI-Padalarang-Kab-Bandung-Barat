<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class JenisPelanggaran extends Model
{
    use SoftDeletes;
    protected $table = 'jenis_pelanggaran';
    protected $primaryKey = 'id_jenispelanggaran';

    protected $fillable = [
        'nama_pelanggaran',
        'tingkat_pelanggaran',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'id_jenispelanggaran');
    }
}
