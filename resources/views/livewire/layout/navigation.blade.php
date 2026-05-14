<?php

use App\Livewire\Actions\Logout;
use App\Models\Notifikasi;
use Livewire\Volt\Component;

new class extends Component
{
    public int $unreadCount = 0;
    public $notifikasis = [];

    public function mount(): void
    {
        $this->loadNotifikasi();
    }

    public function loadNotifikasi(): void
    {
        $user = auth()->user();

        $this->notifikasis = Notifikasi::forUser($user->id_pengguna)
            ->with('pelanggaran.siswa')
            ->where('status', 'terkirim')
            ->orderBy('waktu_dikirim', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        $this->unreadCount = Notifikasi::forUser($user->id_pengguna)
            ->where('status', 'terkirim')
            ->unread()
            ->count();
    }

    public function markAsRead(int $id): void
    {
        $notif = Notifikasi::find($id);

        if ($notif && $notif->id_pengguna === auth()->user()->id_pengguna
            && $notif->status === 'terkirim') {
            $notif->markAsRead();
            $this->loadNotifikasi();
        }
    }

    public function markAllRead(): void
    {
        Notifikasi::forUser(auth()->user()->id_pengguna)
            ->where('status', 'terkirim')
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

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
                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-md hover:bg-[#F0F4FB] transition-colors duration-200"
                >
                    <svg class="w-6 h-6 text-[#0D2D6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img src="{{ asset('images/logo_simdis.png') }}" alt="Logo SIMDIS"
                         class="h-16 w-auto object-contain">
                </a>
            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-3">

                {{-- ── BELL NOTIFIKASI dengan Pusher realtime ── --}}
                <div class="relative"
                     x-data="{
                        open: false,
                        unreadCount: {{ $unreadCount }},
                        notifikasis: {{ json_encode($notifikasis) }},

                        init() {
                            // Subscribe ke private channel user ini
                            const userId = {{ auth()->user()->id_pengguna }};

                            window.Echo.private('notifikasi.' + userId)
                                .listen('.NotifikasiBaru', (e) => {
                                    const notif = e.notifikasi;

                                    // Tambah ke awal list
                                    this.notifikasis.unshift(notif);

                                    // Batasi hanya 10 item
                                    if (this.notifikasis.length > 10) {
                                        this.notifikasis = this.notifikasis.slice(0, 10);
                                    }

                                    // Naikkan badge
                                    this.unreadCount++;

                                    // Bunyi notif (opsional)
                                    this.playSound();
                                });
                        },

                        playSound() {
                            try {
                                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                                const o   = ctx.createOscillator();
                                const g   = ctx.createGain();
                                o.connect(g);
                                g.connect(ctx.destination);
                                o.frequency.value = 520;
                                g.gain.setValueAtTime(0.3, ctx.currentTime);
                                g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                                o.start(ctx.currentTime);
                                o.stop(ctx.currentTime + 0.4);
                            } catch(e) {}
                        },

                        markRead(id) {
                            const item = this.notifikasis.find(n => n.id_notifikasi == id);
                            if (item && !item.is_read) {
                                item.is_read = true;
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

                        diffForHumans(dateStr) {
                            if (!dateStr) return '';
                            const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
                            if (diff < 60)    return diff + ' detik yang lalu';
                            if (diff < 3600)  return Math.floor(diff/60) + ' menit yang lalu';
                            if (diff < 86400) return Math.floor(diff/3600) + ' jam yang lalu';
                            return Math.floor(diff/86400) + ' hari yang lalu';
                        }
                     }"
                     @click.outside="open = false">

                    {{-- Tombol Bell --}}
                    <button @click="open = !open"
                        class="relative p-2 rounded-lg text-[#0D2D6B] hover:bg-[#F0F4FB]
                               transition-colors duration-200"
                        title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                                     6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                     0 11-6 0v-1m6 0H9" />
                        </svg>

                        {{-- Badge --}}
                        <span x-show="unreadCount > 0"
                              x-text="unreadCount > 9 ? '9+' : unreadCount"
                              class="absolute top-1 right-1 flex items-center justify-center
                                     w-4 h-4 text-[10px] font-bold text-white bg-red-500
                                     rounded-full leading-none">
                        </span>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl
                                border border-[rgba(13,45,107,0.08)] z-50 overflow-hidden"
                         style="top: 100%">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3
                                    bg-gradient-to-r from-[#0D2D6B] to-[#163580]">
                            <h6 class="text-white text-sm font-bold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                             6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                                             6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                             0 11-6 0v-1m6 0H9" />
                                </svg>
                                Notifikasi
                                <span x-show="unreadCount > 0"
                                      x-text="unreadCount"
                                      class="bg-[#F5B800] text-[#0D2D6B] text-[10px]
                                             font-bold px-1.5 py-0.5 rounded-full">
                                </span>
                            </h6>
                            <button x-show="unreadCount > 0"
                                    @click="markAllRead()"
                                    class="text-[#F5B800] text-xs font-semibold underline
                                           hover:text-yellow-300 transition-colors">
                                Tandai semua dibaca
                            </button>
                        </div>

                        {{-- List --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-[#f0f4fb]">
                            <template x-if="notifikasis.length === 0">
                                <div class="py-10 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm">Tidak ada notifikasi</p>
                                </div>
                            </template>

                            <template x-for="notif in notifikasis" :key="notif.id_notifikasi">
                                <button
                                    @click="markRead(notif.id_notifikasi)"
                                    class="w-full text-left flex gap-3 px-4 py-3 transition-colors hover:bg-[#f0f4fb]"
                                    :class="!notif.is_read ? 'bg-amber-50 border-l-4 border-l-[#F5B800]' : ''"
                                >
                                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center
                                                justify-center text-base"
                                         :class="notif.isi_pesan && notif.isi_pesan.includes('PEMANGGILAN')
                                                 ? 'bg-red-100' : 'bg-amber-100'">
                                        <span x-text="notif.isi_pesan && notif.isi_pesan.includes('PEMANGGILAN') ? '🚨' : '⚠️'"></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-800 leading-snug line-clamp-3 text-left"
                                           x-text="notif.isi_pesan"></p>
                                        <p class="text-[11px] text-gray-400 mt-1 text-left"
                                           x-text="diffForHumans(notif.waktu_dikirim)"></p>
                                    </div>

                                    <div x-show="!notif.is_read" class="flex-shrink-0 mt-1.5">
                                        <span class="w-2 h-2 bg-[#F5B800] rounded-full block"></span>
                                    </div>
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
                </div>
                {{-- ── END BELL NOTIFIKASI ── --}}

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