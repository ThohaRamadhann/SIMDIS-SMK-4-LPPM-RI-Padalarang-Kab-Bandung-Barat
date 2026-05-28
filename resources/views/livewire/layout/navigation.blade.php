<?php
use App\Livewire\Actions\Logout;
use App\Models\Notifikasi;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->unreadCount = Notifikasi::forUser(auth()->user()->id_pengguna)
            ->where('status', 'terkirim')
            ->unread()
            ->count();
    }

    #[On('notif-count-changed')]
    public function updateUnreadCount(int $count): void
    {
        $this->unreadCount = $count;
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

                {{-- Bell --}}
<div class="relative" wire:ignore x-data="{ count: {{ $unreadCount }} }"
x-on:notif-count-update.window="count = $event.detail.count">
<button type="button"
    @click="window.dispatchEvent(new CustomEvent('open-notif-sidebar'))"
    class="relative p-2 rounded-lg text-[#0D2D6B] hover:bg-[#F0F4FB] transition-colors duration-200"
    title="Notifikasi">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
               6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
               6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
               0 11-6 0v-1m6 0H9" />
    </svg>
    <span x-show="count > 0"
        x-text="count > 9 ? '9+' : count"
        class="absolute top-1 right-1 flex items-center justify-center
               w-4 h-4 text-[10px] font-bold text-white bg-red-500
               rounded-full leading-none pointer-events-none">
    </span>
</button>
</div>

                {{-- Avatar + Nama --}}
                <a href="{{ route('profile') }}" wire:navigate
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg
                          hover:bg-[#F0F4FB] transition-colors duration-200 group">
                    <div
                        class="w-9 h-9 rounded-full bg-[#0D2D6B] text-white
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
