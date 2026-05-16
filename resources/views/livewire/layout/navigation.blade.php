<?php

use App\Livewire\Actions\Logout;
use App\Models\Notifikasi;
use Livewire\Volt\Component;

new class extends Component
{
    public int   $unreadCount = 0;
    public array $notifikasis = [];
    public string $roleUser   = '';

    public function mount(): void
    {
        $this->roleUser = optional(auth()->user()->role)->nama_role ?? '';
        $this->loadNotifikasi();
    }

    public function loadNotifikasi(): void
    {
        $user = auth()->user();

        $this->notifikasis = Notifikasi::forUser($user->id_pengguna)
            ->with([
                'pelanggaran.siswa',
                'pelanggaran.jenisPelanggaran',
            ])
            ->where('status', 'terkirim')
            ->orderBy('waktu_dikirim', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notif) {
                $statusPenerima = [];

                if (
                    $notif->id_pelanggaran &&
                    in_array($this->roleUser, ['guru_bk', 'wali_kelas'])
                ) {
                    $statusPenerima = $this->getStatusPenerima($notif->id_pelanggaran);
                }

                return [
                    'id_notifikasi'    => $notif->id_notifikasi,
                    'id_pelanggaran'   => $notif->id_pelanggaran,
                    'isi_pesan'        => $notif->isi_pesan,
                    'jenis_notifikasi' => $notif->jenis_notifikasi,
                    'waktu_dikirim'    => optional($notif->waktu_dikirim)->toDateTimeString(),
                    'status'           => $notif->status,
                    'is_read'          => $notif->is_read,
                    'read_at'          => optional($notif->read_at)->toDateTimeString(),
                    'nama_siswa'       => optional(optional($notif->pelanggaran)->siswa)->nama ?? null,
                    'nis_siswa'        => optional(optional($notif->pelanggaran)->siswa)->nis  ?? null,
                    'nama_pelanggaran' => optional(optional($notif->pelanggaran)->jenisPelanggaran)->nama_pelanggaran ?? null,
                    'tingkat'          => optional(optional($notif->pelanggaran)->jenisPelanggaran)->tingkat_pelanggaran ?? null,
                    'waktu_kejadian'   => optional(optional($notif->pelanggaran)->waktu_kejadian)?->toDateTimeString() ?? null,
                    'deskripsi'        => optional($notif->pelanggaran)->deskripsi ?? null,
                    'status_pembinaan' => optional($notif->pelanggaran)->status_pembinaan ?? null,
                    'status_penerima'  => $statusPenerima,
                ];
            })
            ->toArray();

        $this->unreadCount = Notifikasi::forUser($user->id_pengguna)
            ->where('status', 'terkirim')
            ->unread()
            ->count();
    }

    private function getStatusPenerima(int $idPelanggaran): array
    {
        return Notifikasi::where('id_pelanggaran', $idPelanggaran)
            ->where('status', 'terkirim')
            ->with(['pengguna.role'])
            ->get()
            ->map(function ($n) {
                $roleName  = optional(optional($n->pengguna)->role)->nama_role ?? '-';
                $labelRole = match ($roleName) {
                    'orang_tua'  => 'Orang Tua',
                    'wali_kelas' => 'Wali Kelas',
                    'guru_bk'    => 'Guru BK',
                    default      => ucfirst(str_replace('_', ' ', $roleName)),
                };

                return [
                    'nama'    => optional($n->pengguna)->name ?? '-',
                    'role'    => $labelRole,
                    'is_read' => (bool) $n->is_read,
                    'read_at' => optional($n->read_at)?->toDateTimeString(),
                ];
            })
            ->toArray();
    }

    public function markAsRead(int $id): void
    {
        $notif = Notifikasi::find($id);
        if ($notif && $notif->id_pengguna === auth()->user()->id_pengguna && $notif->status === 'terkirim') {
            $notif->markAsRead();
            $this->loadNotifikasi();
        }
    }

    public function markAllRead(): void
    {
        Notifikasi::forUser(auth()->user()->id_pengguna)
            ->where('status', 'terkirim')
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);
        $this->loadNotifikasi();
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
};
?>

<nav class="bg-white border-b border-[rgba(245,184,0,0.3)] shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">
                <button @click="$dispatch('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-md hover:bg-[#F0F4FB] transition-colors duration-200">
                    <svg class="w-6 h-6 text-[#0D2D6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img src="{{ asset('images/logo_simdis.png') }}" alt="Logo SIMDIS"
                         class="h-16 w-auto object-contain">
                </a>
            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-3">

                {{-- ══════════════════════════════════════════
                     BELL NOTIFIKASI
                ══════════════════════════════════════════ --}}
                <div class="relative"
                     x-data="{
                        open: false,
                        showDetail: false,
                        activeNotif: null,
                        unreadCount: {{ $unreadCount }},
                        notifikasis: {{ Js::from($notifikasis) }},
                        roleUser: '{{ $roleUser }}',

                        init() {
                            const userId = {{ auth()->user()->id_pengguna }};
                            window.Echo.private('notifikasi.' + userId)
                                .listen('.NotifikasiBaru', (e) => {
                                    const notif = e.notifikasi;
                                    this.notifikasis.unshift(notif);
                                    if (this.notifikasis.length > 10)
                                        this.notifikasis = this.notifikasis.slice(0, 10);
                                    this.unreadCount++;
                                    this.playSound();
                                });
                        },

                        playSound() {
                            try {
                                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                                const o = ctx.createOscillator(), g = ctx.createGain();
                                o.connect(g); g.connect(ctx.destination);
                                o.frequency.value = 520;
                                g.gain.setValueAtTime(0.3, ctx.currentTime);
                                g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                                o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.4);
                            } catch(e) {}
                        },

                        openDetail(notif) {
                            this.activeNotif = notif;
                            this.showDetail  = true;
                            this.open        = false;
                            if (!notif.is_read) this.markRead(notif.id_notifikasi);
                        },

                        closeDetail() {
                            this.showDetail  = false;
                            this.activeNotif = null;
                        },

                        markRead(id) {
                            const item = this.notifikasis.find(n => n.id_notifikasi == id);
                            if (item && !item.is_read) {
                                item.is_read = true;
                                if (this.activeNotif && this.activeNotif.id_notifikasi == id)
                                    this.activeNotif.is_read = true;
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                                fetch('/notifikasi/' + id + '/read', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Content-Type': 'application/json',
                                    }
                                });
                            }
                        },

                        markAllRead() {
                            this.notifikasis.forEach(n => n.is_read = true);
                            this.unreadCount = 0;
                            fetch('/notifikasi/read-all', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Content-Type': 'application/json',
                                }
                            });
                        },

                        // Hitung berapa penerima yang belum baca
                        belumBacaCount(statusPenerima) {
                            if (!statusPenerima || !statusPenerima.length) return 0;
                            return statusPenerima.filter(p => !p.is_read).length;
                        },

                        // Penerima yang belum baca saja (untuk chip di list)
                        belumBacaList(statusPenerima) {
                            if (!statusPenerima || !statusPenerima.length) return [];
                            return statusPenerima.filter(p => !p.is_read);
                        },

                        diffForHumans(dateStr) {
                            if (!dateStr) return '';
                            const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
                            if (diff < 60)    return diff + ' detik yang lalu';
                            if (diff < 3600)  return Math.floor(diff/60) + ' menit yang lalu';
                            if (diff < 86400) return Math.floor(diff/3600) + ' jam yang lalu';
                            return Math.floor(diff/86400) + ' hari yang lalu';
                        },

                        formatDate(dateStr) {
                            if (!dateStr) return '-';
                            return new Date(dateStr).toLocaleDateString('id-ID', {
                                weekday: 'long', day: 'numeric', month: 'long',
                                year: 'numeric', hour: '2-digit', minute: '2-digit'
                            });
                        },

                        tingkatColor(t) {
                            if (!t) return '#6b7280';
                            const v = t.toLowerCase();
                            return v === 'berat' ? '#ef4444' : v === 'sedang' ? '#f97316' : '#eab308';
                        },

                        tingkatBg(t) {
                            if (!t) return '#f3f4f6';
                            const v = t.toLowerCase();
                            return v === 'berat' ? '#fee2e2' : v === 'sedang' ? '#fff7ed' : '#fefce8';
                        },

                        roleColor(role) {
                            if (role === 'Orang Tua')  return { bg: '#f3e8ff', text: '#7e22ce' };
                            if (role === 'Wali Kelas') return { bg: '#dbeafe', text: '#1d4ed8' };
                            if (role === 'Guru BK')    return { bg: '#ccfbf1', text: '#0f766e' };
                            return { bg: '#f3f4f6', text: '#374151' };
                        },

                        dropdownStyle() {
                            if (window.innerWidth < 640)
                                return 'position:fixed; top:64px; left:0.5rem; right:0.5rem; width:auto; max-width:100%;';
                            return 'top:100%; right:0; width:min(440px, calc(100vw - 1rem));';
                        }
                     }"
                     @click.outside="open = false"
                     @keydown.escape.window="showDetail = false; open = false">

                    {{-- Tombol Bell --}}
                    <button @click="open = !open"
                        class="relative p-2 rounded-lg text-[#0D2D6B] hover:bg-[#F0F4FB] transition-colors duration-200"
                        title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                                     6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                     0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadCount > 0" style="display:none"
                              x-text="unreadCount > 9 ? '9+' : unreadCount"
                              class="absolute top-1 right-1 flex items-center justify-center
                                     w-4 h-4 text-[10px] font-bold text-white bg-red-500
                                     rounded-full leading-none">
                        </span>
                    </button>

                    {{-- ── Dropdown ── --}}
                    <div x-show="open" style="display:none"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute bg-white rounded-xl shadow-xl
                                border border-[rgba(13,45,107,0.08)] z-50 overflow-hidden"
                         :style="dropdownStyle()">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3
                                    bg-gradient-to-r from-[#0D2D6B] to-[#163580]">
                            <h6 class="text-white text-sm font-bold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                             6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                                             6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                             0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Notifikasi
                                <span x-show="unreadCount > 0" style="display:none"
                                      x-text="unreadCount"
                                      class="bg-[#F5B800] text-[#0D2D6B] text-[10px]
                                             font-bold px-1.5 py-0.5 rounded-full">
                                </span>
                            </h6>
                            <button x-show="unreadCount > 0" style="display:none"
                                    @click="markAllRead()"
                                    class="text-[#F5B800] text-xs font-semibold underline
                                           hover:text-yellow-300 transition-colors">
                                Tandai semua dibaca
                            </button>
                        </div>

                        {{-- List --}}
                        <div class="max-h-[420px] overflow-y-auto divide-y divide-[#f0f4fb]">

                            <template x-if="notifikasis.length === 0">
                                <div class="py-10 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm">Tidak ada notifikasi</p>
                                </div>
                            </template>

                            <template x-for="notif in notifikasis" :key="notif.id_notifikasi">
                                <button
                                    @click="openDetail(notif)"
                                    class="w-full text-left px-4 py-3 transition-colors
                                           hover:bg-[#f0f4fb] group flex flex-col gap-2"
                                    :class="!notif.is_read
                                            ? 'bg-amber-50 border-l-4 border-l-[#F5B800]'
                                            : 'border-l-4 border-l-transparent'">

                                    {{-- Baris atas: icon + konten utama + dot --}}
                                    <div class="flex gap-3 w-full">
                                        {{-- Icon --}}
                                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center
                                                    justify-center text-base"
                                             :class="notif.isi_pesan && notif.isi_pesan.includes('PEMANGGILAN')
                                                     ? 'bg-red-100' : 'bg-amber-100'">
                                            <span x-text="notif.isi_pesan && notif.isi_pesan.includes('PEMANGGILAN') ? '🚨' : '⚠️'"></span>
                                        </div>

                                        {{-- Konten --}}
                                        <div class="flex-1 min-w-0">
                                            <p x-show="notif.nama_siswa" style="display:none"
                                               class="text-xs font-bold text-[#0D2D6B] truncate"
                                               x-text="notif.nama_siswa + (notif.nis_siswa ? ' · ' + notif.nis_siswa : '')">
                                            </p>
                                            <p x-show="notif.nama_pelanggaran" style="display:none"
                                               class="text-[11px] font-semibold truncate"
                                               :style="'color:' + tingkatColor(notif.tingkat)"
                                               x-text="notif.nama_pelanggaran + (notif.tingkat ? ' · ' + notif.tingkat : '')">
                                            </p>
                                            <p class="text-xs text-gray-600 leading-snug line-clamp-2 text-left mt-0.5"
                                               x-text="notif.isi_pesan"></p>
                                            <p class="text-[11px] text-gray-400 mt-1"
                                               x-text="diffForHumans(notif.waktu_dikirim)"></p>
                                        </div>

                                        {{-- Unread dot --}}
                                        <div x-show="!notif.is_read" style="display:none"
                                             class="flex-shrink-0 mt-1.5">
                                            <span class="w-2 h-2 bg-[#F5B800] rounded-full block"></span>
                                        </div>
                                    </div>

                                    {{-- ── Panel status baca penerima (langsung di list item) ──
                                         Hanya tampil untuk guru_bk & wali_kelas
                                         Tampil chip per penerima: sudah baca = hijau, belum = oranye
                                    --}}
                                    <template x-if="(roleUser === 'guru_bk' || roleUser === 'wali_kelas')
                                                    && notif.status_penerima
                                                    && notif.status_penerima.length > 0">
                                        <div class="ml-12 flex flex-wrap gap-1.5" @click.stop>

                                            <template x-for="(p, i) in notif.status_penerima" :key="i">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5
                                                             rounded-full text-[10px] font-semibold leading-none"
                                                      :style="p.is_read
                                                              ? 'background:#dcfce7; color:#15803d'
                                                              : 'background:#ffedd5; color:#c2410c'">
                                                    <span x-text="p.is_read ? '✓' : '○'"></span>
                                                    <span x-text="p.role"></span>
                                                </span>
                                            </template>

                                        </div>
                                    </template>

                                </button>
                            </template>
                        </div>

                        {{-- Footer --}}
                        <div class="px-4 py-2.5 border-t border-[#f0f4fb] bg-[#fafbff]">
                            <a href="{{ route('notifikasi.index') }}" wire:navigate
                               class="block text-center text-xs font-semibold text-[#0D2D6B]
                                      hover:text-[#163580] transition-colors">
                                Lihat semua notifikasi →
                            </a>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════
                         MODAL DETAIL NOTIFIKASI
                    ══════════════════════════════════════════ --}}
                    <div x-show="showDetail" style="display:none"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click.self="closeDetail()"
                         class="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-4"
                         style="background: rgba(9,30,74,0.55); backdrop-filter: blur(4px);">

                        <div x-show="showDetail"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="bg-white rounded-2xl shadow-2xl w-full overflow-y-auto max-h-[90vh]"
                             style="max-width: min(448px, calc(100vw - 1.5rem));">

                            <template x-if="activeNotif">
                                <div>
                                    {{-- Modal Header --}}
                                    <div class="bg-gradient-to-r from-[#0D2D6B] to-[#163580] px-5 py-4
                                                flex items-center justify-between sticky top-0 z-10">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                        text-xl flex-shrink-0"
                                                 :class="activeNotif.isi_pesan && activeNotif.isi_pesan.includes('PEMANGGILAN')
                                                         ? 'bg-red-500' : 'bg-amber-400'">
                                                <span x-text="activeNotif.isi_pesan && activeNotif.isi_pesan.includes('PEMANGGILAN') ? '🚨' : '⚠️'"></span>
                                            </div>
                                            <div>
                                                <h3 class="text-white font-bold text-sm">Detail Notifikasi</h3>
                                                <p class="text-blue-200 text-xs"
                                                   x-text="diffForHumans(activeNotif.waktu_dikirim)"></p>
                                            </div>
                                        </div>
                                        <button @click="closeDetail()"
                                                class="w-8 h-8 rounded-lg bg-white/15 hover:bg-white/25
                                                       text-white flex items-center justify-center
                                                       transition-colors text-lg leading-none flex-shrink-0">
                                            &times;
                                        </button>
                                    </div>

                                    {{-- Modal Body --}}
                                    <div class="px-4 sm:px-5 py-4 space-y-3">

                                        {{-- Info Siswa --}}
                                        <template x-if="activeNotif.nama_siswa">
                                            <div class="bg-[#f0f4fb] rounded-xl p-3 sm:p-4">
                                                <p class="text-[11px] font-bold text-[#4A5E8A] uppercase tracking-wide mb-2">
                                                    👤 Informasi Siswa
                                                </p>
                                                <p class="text-[#0D2D6B] font-bold text-base"
                                                   x-text="activeNotif.nama_siswa"></p>
                                                <p x-show="activeNotif.nis_siswa" style="display:none"
                                                   class="text-xs text-[#4A5E8A] mt-0.5"
                                                   x-text="'NIS: ' + activeNotif.nis_siswa"></p>
                                            </div>
                                        </template>

                                        {{-- Info Pelanggaran --}}
                                        <template x-if="activeNotif.nama_pelanggaran">
                                            <div class="rounded-xl p-3 sm:p-4 border"
                                                 :style="'background:' + tingkatBg(activeNotif.tingkat) + '; border-color:' + tingkatColor(activeNotif.tingkat) + '40'">
                                                <p class="text-[11px] font-bold uppercase tracking-wide mb-2"
                                                   :style="'color:' + tingkatColor(activeNotif.tingkat)">
                                                    ⚠️ Jenis Pelanggaran
                                                </p>
                                                <p class="font-bold text-sm text-gray-800"
                                                   x-text="activeNotif.nama_pelanggaran"></p>
                                                <span class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-1
                                                             rounded-full text-xs font-bold"
                                                      :style="'background:' + tingkatColor(activeNotif.tingkat) + '20; color:' + tingkatColor(activeNotif.tingkat)">
                                                    <span x-text="activeNotif.tingkat && activeNotif.tingkat.toLowerCase() === 'berat' ? '🔴' :
                                                                  activeNotif.tingkat && activeNotif.tingkat.toLowerCase() === 'sedang' ? '🟠' : '🟡'"></span>
                                                    Tingkat: <span x-text="activeNotif.tingkat"></span>
                                                </span>
                                            </div>
                                        </template>

                                        {{-- Waktu Kejadian --}}
                                        <template x-if="activeNotif.waktu_kejadian">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-[#f0f4fb] flex items-center
                                                            justify-center text-sm flex-shrink-0">🕐</div>
                                                <div>
                                                    <p class="text-[11px] text-[#4A5E8A] font-semibold uppercase tracking-wide">
                                                        Waktu Kejadian
                                                    </p>
                                                    <p class="text-sm text-[#1e3a6e] font-medium mt-0.5"
                                                       x-text="formatDate(activeNotif.waktu_kejadian)"></p>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Deskripsi --}}
                                        <template x-if="activeNotif.deskripsi">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-[#f0f4fb] flex items-center
                                                            justify-center text-sm flex-shrink-0">📝</div>
                                                <div>
                                                    <p class="text-[11px] text-[#4A5E8A] font-semibold uppercase tracking-wide">
                                                        Keterangan
                                                    </p>
                                                    <p class="text-sm text-[#1e3a6e] mt-0.5 leading-relaxed"
                                                       x-text="activeNotif.deskripsi"></p>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Status Pembinaan --}}
                                        <template x-if="activeNotif.status_pembinaan">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-[#f0f4fb] flex items-center
                                                            justify-center text-sm flex-shrink-0">📋</div>
                                                <div>
                                                    <p class="text-[11px] text-[#4A5E8A] font-semibold uppercase tracking-wide">
                                                        Status Pembinaan
                                                    </p>
                                                    <span class="inline-flex items-center mt-0.5 px-2.5 py-1
                                                                 rounded-full text-xs font-bold"
                                                          :class="activeNotif.status_pembinaan === 'Selesai'
                                                                  ? 'bg-green-100 text-green-700'
                                                                  : activeNotif.status_pembinaan === 'Dalam Proses'
                                                                  ? 'bg-blue-100 text-blue-700'
                                                                  : 'bg-orange-100 text-orange-700'"
                                                          x-text="activeNotif.status_pembinaan === 'Selesai' ? '✅ Selesai'
                                                                  : activeNotif.status_pembinaan === 'Dalam Proses' ? '🔄 Dalam Proses'
                                                                  : '⏳ Belum Ditindak'">
                                                    </span>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Isi Pesan --}}
                                        <div class="bg-gray-50 rounded-xl p-3 sm:p-4 border border-gray-100">
                                            <p class="text-[11px] text-[#4A5E8A] font-semibold uppercase tracking-wide mb-1.5">
                                                💬 Pesan Notifikasi
                                            </p>
                                            <p class="text-sm text-gray-700 leading-relaxed"
                                               x-text="activeNotif.isi_pesan"></p>
                                        </div>

                                        {{-- ══════════════════════════════════════════
                                             PANEL STATUS BACA — detail per penerima
                                             Hanya guru_bk & wali_kelas
                                        ══════════════════════════════════════════ --}}
                                        <template x-if="(roleUser === 'guru_bk' || roleUser === 'wali_kelas')
                                                        && activeNotif.status_penerima
                                                        && activeNotif.status_penerima.length > 0">

                                            <div class="border border-[rgba(13,45,107,0.12)] rounded-xl overflow-hidden">

                                                {{-- Header --}}
                                                <div class="bg-[#f0f4fb] px-3 py-2.5 flex items-center gap-2
                                                            border-b border-[rgba(13,45,107,0.08)]">
                                                    <svg class="w-3.5 h-3.5 text-[#0D2D6B]" fill="none"
                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0
                                                                 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542
                                                                 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <p class="text-[11px] font-bold text-[#0D2D6B] uppercase tracking-wide">
                                                        Status Baca Penerima
                                                    </p>
                                                    {{-- Ringkasan: X sudah / Y belum --}}
                                                    <span class="ml-auto text-[10px] text-gray-500"
                                                          x-text="activeNotif.status_penerima.filter(p => p.is_read).length
                                                                   + ' sudah · '
                                                                   + activeNotif.status_penerima.filter(p => !p.is_read).length
                                                                   + ' belum'">
                                                    </span>
                                                </div>

                                                {{-- Baris per penerima --}}
                                                <div class="divide-y divide-[#f0f4fb]">
                                                    <template x-for="(penerima, idx) in activeNotif.status_penerima"
                                                              :key="idx">
                                                        <div class="flex items-center justify-between px-3 py-2.5 bg-white">

                                                            {{-- Avatar + nama + role --}}
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <div class="w-7 h-7 rounded-full flex items-center
                                                                            justify-center text-[11px] font-bold flex-shrink-0"
                                                                     :style="'background:' + roleColor(penerima.role).bg
                                                                             + '; color:' + roleColor(penerima.role).text"
                                                                     x-text="penerima.nama ? penerima.nama.charAt(0).toUpperCase() : '?'">
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="text-xs font-semibold text-gray-800 truncate"
                                                                       x-text="penerima.nama"></p>
                                                                    <p class="text-[10px] truncate"
                                                                       :style="'color:' + roleColor(penerima.role).text"
                                                                       x-text="penerima.role"></p>
                                                                </div>
                                                            </div>

                                                            {{-- Status + waktu baca --}}
                                                            <div class="flex-shrink-0 ml-2 text-right">
                                                                <template x-if="penerima.is_read">
                                                                    <div>
                                                                        <span class="inline-flex items-center gap-1
                                                                                     px-2 py-0.5 rounded-full
                                                                                     text-[10px] font-semibold
                                                                                     bg-green-100 text-green-700">
                                                                            ✓ Sudah dibaca
                                                                        </span>
                                                                        <p x-show="penerima.read_at" style="display:none"
                                                                           class="text-[10px] text-gray-400 mt-0.5"
                                                                           x-text="formatDate(penerima.read_at)">
                                                                        </p>
                                                                    </div>
                                                                </template>
                                                                <template x-if="!penerima.is_read">
                                                                    <span class="inline-flex items-center gap-1
                                                                                 px-2 py-0.5 rounded-full
                                                                                 text-[10px] font-semibold
                                                                                 bg-orange-100 text-orange-700">
                                                                        ○ Belum dibaca
                                                                    </span>
                                                                </template>
                                                            </div>

                                                        </div>
                                                    </template>
                                                </div>

                                            </div>
                                        </template>
                                        {{-- END PANEL STATUS BACA --}}

                                    </div>

                                    {{-- Modal Footer --}}
                                    <div class="px-4 sm:px-5 pb-5 pt-1">
                                        <a href="{{ route('pelanggaran.index') }}" wire:navigate
                                           @click="closeDetail()"
                                           class="flex items-center justify-center gap-2 w-full
                                                  bg-gradient-to-r from-[#0D2D6B] to-[#163580]
                                                  text-white text-sm font-bold py-3 rounded-xl
                                                  hover:from-[#163580] hover:to-[#1e45a0]
                                                  transition-all duration-200 shadow-md">
                                            Lihat Data Pelanggaran →
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    {{-- END MODAL DETAIL --}}

                </div>
                {{-- END BELL NOTIFIKASI --}}

                {{-- Avatar + Nama --}}
                <a href="{{ route('profile') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-1.5 rounded-lg
                          hover:bg-[#F0F4FB] transition-colors duration-200 group">
                    <div class="w-9 h-9 rounded-full bg-[#0D2D6B] text-white
                                flex items-center justify-center font-bold text-sm shadow-sm
                                group-hover:bg-[#163580] transition-colors duration-200">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-[#0D2D6B]">
                        {{ auth()->user()->name }}
                    </span>
                </a>

                {{-- Logout --}}
                <button wire:click="logout"
                    class="text-sm font-medium text-[#4A5E8A] hover:text-red-500
                           transition-colors duration-200 px-2 py-1 rounded-md hover:bg-red-50">
                    Logout
                </button>

            </div>
        </div>
    </div>
</nav>