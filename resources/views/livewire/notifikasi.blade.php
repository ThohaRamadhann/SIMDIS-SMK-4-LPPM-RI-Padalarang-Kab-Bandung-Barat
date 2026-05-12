{{--
    Komponen bell notifikasi untuk navbar.
    Cara pakai: @include('components.notifikasi-bell')
--}}

@php
    $user          = Auth::user();
    $notifikasis   = $user->notifikasi()
                        ->with('pelanggaran.siswa')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
    $unreadCount   = $notifikasis->where('is_read', false)->count();
@endphp

<style>
    .notif-bell-wrapper {
        position: relative;
        display: inline-block;
    }

    .notif-bell-btn {
        background: none;
        border: none;
        cursor: pointer;
        position: relative;
        padding: 0.4rem;
        color: var(--navy, #0D2D6B);
        font-size: 1.2rem;
    }

    .notif-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #e53e3e;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .notif-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 360px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(13, 45, 107, 0.18);
        border: 1px solid rgba(13, 45, 107, 0.08);
        z-index: 9999;
        overflow: hidden;
    }

    .notif-dropdown.open {
        display: block;
    }

    .notif-header {
        padding: 0.85rem 1rem;
        background: linear-gradient(90deg, #0D2D6B, #163580);
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .notif-header h6 {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .notif-mark-all {
        font-size: 0.72rem;
        color: #F5B800;
        cursor: pointer;
        background: none;
        border: none;
        font-weight: 600;
        text-decoration: underline;
    }

    .notif-list {
        max-height: 320px;
        overflow-y: auto;
    }

    .notif-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f4fb;
        display: flex;
        gap: 0.65rem;
        align-items: flex-start;
        transition: background 0.15s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .notif-item:hover {
        background: #f0f4fb;
    }

    .notif-item.unread {
        background: #fffbeb;
        border-left: 3px solid #F5B800;
    }

    .notif-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fff3cd;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.85rem;
    }

    .notif-icon.panggil { background: #ffe4e4; }
    .notif-icon.pembinaan { background: #fff3cd; }

    .notif-body p {
        margin: 0 0 0.2rem;
        font-size: 0.8rem;
        line-height: 1.4;
        color: #1a202c;
    }

    .notif-body small {
        font-size: 0.7rem;
        color: #718096;
    }

    .notif-empty {
        padding: 2rem 1rem;
        text-align: center;
        color: #a0aec0;
        font-size: 0.82rem;
    }

    .notif-footer {
        padding: 0.6rem 1rem;
        text-align: center;
        border-top: 1px solid #f0f4fb;
    }

    .notif-footer a {
        font-size: 0.78rem;
        color: #0D2D6B;
        font-weight: 600;
        text-decoration: none;
    }
</style>

<div class="notif-bell-wrapper" id="notifWrapper">

    {{-- Tombol Bell --}}
    <button class="notif-bell-btn" id="notifToggle" title="Notifikasi">
        <i class="fas fa-bell"></i>
        @if ($unreadCount > 0)
            <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div class="notif-dropdown" id="notifDropdown">

        <div class="notif-header">
            <h6><i class="fas fa-bell me-1"></i> Notifikasi</h6>
            @if ($unreadCount > 0)
                <button class="notif-mark-all" id="markAllRead">Tandai semua dibaca</button>
            @endif
        </div>

        <div class="notif-list">
            @forelse ($notifikasis as $notif)
                @php
                    $isPanggil = str_contains($notif->isi_pesan, 'PEMANGGILAN');
                    $iconClass = $isPanggil ? 'panggil' : 'pembinaan';
                    $icon      = $isPanggil ? '🚨' : '⚠️';
                @endphp
                <div class="notif-item {{ $notif->is_read ? '' : 'unread' }}"
                     data-id="{{ $notif->id_notifikasi }}"
                     onclick="bacaNotif({{ $notif->id_notifikasi }}, this)">
                    <div class="notif-icon {{ $iconClass }}">{{ $icon }}</div>
                    <div class="notif-body">
                        <p>{{ $notif->isi_pesan }}</p>
                        <small>{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="notif-empty">
                    <i class="fas fa-check-circle" style="font-size:1.5rem;margin-bottom:.5rem;display:block;color:#a0aec0"></i>
                    Tidak ada notifikasi
                </div>
            @endforelse
        </div>

        <div class="notif-footer">
            <a href="{{ route('notifikasi.index') }}">Lihat semua notifikasi →</a>
        </div>

    </div>
</div>

<script>
    // Toggle dropdown
    document.getElementById('notifToggle').addEventListener('click', function (e) {
        e.stopPropagation();
        document.getElementById('notifDropdown').classList.toggle('open');
    });

    // Tutup jika klik di luar
    document.addEventListener('click', function () {
        document.getElementById('notifDropdown').classList.remove('open');
    });

    // Tandai satu notif dibaca
    function bacaNotif(id, el) {
        el.classList.remove('unread');
        fetch(`/notifikasi/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
    }

    // Tandai semua dibaca
    const markAllBtn = document.getElementById('markAllRead');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
            fetch('/notifikasi/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });
            this.remove();
        });
    }
</script>