<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table      = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_pengguna',
        'id_pelanggaran',
        'isi_pesan',
        'jenis_notifikasi',
        'waktu_dikirim',
        'status',
        'is_read',
        'read_at',
        'pesan_error',
    ];

    protected $casts = [
        'is_read'      => 'boolean',
        'waktu_dikirim' => 'datetime',
        'read_at'      => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function pelanggaran()
    {
        return $this->belongsTo(Pelanggaran::class, 'id_pelanggaran');
    }

    // ── Scope ───────────────────────────────────────────

    /** Notifikasi yang belum dibaca */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /** Notifikasi milik user tertentu */
    public function scopeForUser($query, $idPengguna)
    {
        return $query->where('id_pengguna', $idPengguna);
    }

    // ── Helper ──────────────────────────────────────────

    /** Tandai notifikasi sudah dibaca */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}