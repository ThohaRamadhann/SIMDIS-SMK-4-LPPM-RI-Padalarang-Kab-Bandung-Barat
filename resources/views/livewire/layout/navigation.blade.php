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
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        $this->unreadCount = Notifikasi::forUser($user->id_pengguna)
            ->unread()
            ->count();
    }

    public function markAsRead(int $id): void
    {
        $notif = Notifikasi::find($id);

        if ($notif && $notif->id_pengguna === auth()->user()->id_pengguna) {
            $notif->markAsRead();
            $this->loadNotifikasi();
        }
    }

    public function markAllRead(): void
    {
        Notifikasi::forUser(auth()->user()->id_pengguna)
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

                {{-- HAMBURGER (MOBILE) --}}
                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-md hover:bg-[#F0F4FB] transition-colors duration-200"
                >
                    <svg class="w-6 h-6 text-[#0D2D6B]" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- LOGO SIMDIS --}}
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img
                        src="{{ asset('images/logo_simdis.png') }}"
                        alt="Logo SIMDIS"
                        class="h-16 w-auto object-contain"
                    >
                </a>

            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-3">

                {{-- ── BELL NOTIFIKASI ── --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">

                    {{-- Tombol Bell --}}
                    <button
                        @click="open = !open"
                        class="relative p-2 rounded-lg text-[#0D2D6B] hover:bg-[#F0F4FB]
                               transition-colors duration-200"
                        title="Notifikasi"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                                     6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                     0 11-6 0v-1m6 0H9" />
                        </svg>

                        {{-- Badge unread --}}
                        @if ($unreadCount > 0)
                            <span class="absolute top-1 right-1 flex items-center justify-center
                                         w-4 h-4 text-[10px] font-bold text-white bg-red-500
                                         rounded-full leading-none">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl
                               border border-[rgba(13,45,107,0.08)] z-50 overflow-hidden"
                        style="top: 100%"
                    >
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
                                @if ($unreadCount > 0)
                                    <span class="bg-[#F5B800] text-[#0D2D6B] text-[10px]
                                                 font-bold px-1.5 py-0.5 rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </h6>

                            @if ($unreadCount > 0)
                                <button
                                    wire:click="markAllRead"
                                    class="text-[#F5B800] text-xs font-semibold underline
                                           hover:text-yellow-300 transition-colors"
                                >
                                    Tandai semua dibaca
                                </button>
                            @endif
                        </div>

                        {{-- List Notifikasi --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-[#f0f4fb]">
                            @forelse ($notifikasis as $notif)
                                @php
                                    $isPanggil = str_contains($notif['isi_pesan'], 'PEMANGGILAN');
                                @endphp
                                <button
                                    wire:click="markAsRead({{ $notif['id_notifikasi'] }})"
                                    class="w-full text-left flex gap-3 px-4 py-3 transition-colors
                                           hover:bg-[#f0f4fb]
                                           {{ ! $notif['is_read'] ? 'bg-amber-50 border-l-4 border-l-[#F5B800]' : '' }}"
                                >
                                    {{-- Icon --}}
                                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center
                                                justify-center text-base
                                                {{ $isPanggil ? 'bg-red-100' : 'bg-amber-100' }}">
                                        {{ $isPanggil ? '🚨' : '⚠️' }}
                                    </div>

                                    {{-- Teks --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-800 leading-snug line-clamp-3 text-left">
                                            {{ $notif['isi_pesan'] }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-1 text-left">
                                            {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Dot unread --}}
                                    @if (! $notif['is_read'])
                                        <div class="flex-shrink-0 mt-1.5">
                                            <span class="w-2 h-2 bg-[#F5B800] rounded-full block"></span>
                                        </div>
                                    @endif
                                </button>
                            @empty
                                <div class="py-10 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm">Tidak ada notifikasi</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        <div class="px-4 py-2.5 border-t border-[#f0f4fb] bg-[#fafbff]">
                            <a
                                href="{{ route('notifikasi.index') }}"
                                wire:navigate
                                class="block text-center text-xs font-semibold text-[#0D2D6B]
                                       hover:text-[#163580] transition-colors"
                            >
                                Lihat semua notifikasi →
                            </a>
                        </div>

                    </div>
                </div>
                {{-- ── END BELL NOTIFIKASI ── --}}

                {{-- Avatar + Nama --}}
                <a href="{{ route('profile') }}"
                   wire:navigate
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
                <button
                    wire:click="logout"
                    class="text-sm font-medium text-[#4A5E8A] hover:text-red-500
                           transition-colors duration-200 px-2 py-1 rounded-md
                           hover:bg-red-50"
                >
                    Logout
                </button>

            </div>

        </div>
    </div>
</nav>