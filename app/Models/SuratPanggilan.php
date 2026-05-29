<?php
// app/Models/SuratPanggilan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SuratPanggilan extends Model
{
    protected $table      = 'surat_panggilan';
    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'id_pelanggaran',
        'id_walikelas',
        'nomor_surat',
        'tanggal_panggilan',
        'waktu_panggilan',
        'tempat',
    ];

    protected $casts = [
        'tanggal_panggilan' => 'date',
    ];

    public function pelanggaran()
    {
        return $this->belongsTo(Pelanggaran::class, 'id_pelanggaran');
    }

    public function waliKelas()
    {
        return $this->belongsTo(WaliKelas::class, 'id_walikelas');
    }

    /**
     * Generate nomor surat otomatis
     * Format: {urutan}/SP/SMK-4/PPM/{bulan_romawi}/{tahun}
     */
    public static function generateNomor(): string
    {
        $bulanRomawi = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];

        $tahun  = Carbon::now()->year;
        $bulan  = Carbon::now()->month;
        $urutan = static::whereYear('created_at', $tahun)->count() + 1;

        return sprintf(
            '%04d/SP/SMK-4/PPM/%s/%d',
            $urutan,
            $bulanRomawi[$bulan],
            $tahun
        );
    }
}