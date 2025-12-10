<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
};
?>

<nav class="bg-white border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">
                {{-- HAMBURGER SIDEBAR (MOBILE) --}}
                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-md hover:bg-gray-100"
                >
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- LOGO --}}
                <x-application-logo class="h-8 w-auto text-gray-800" />
            </div>

            {{-- RIGHT (AVATAR + LOGOUT) --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('profile') }}" class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-indigo-500 text-white
                                flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm text-gray-700">
                        {{ auth()->user()->name }}
                    </span>
                </a>

                <button
                    wire:click="logout"
                    class="text-sm text-gray-500 hover:text-gray-700"
                >
                    Logout
                </button>
            </div>

        </div>
    </div>
</nav>
