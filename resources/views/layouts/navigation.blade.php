<nav class="bg-white border-b">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">
                {{-- HAMBURGER (MOBILE) --}}
                <button
                    @click="$dispatch('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-md hover:bg-gray-100"
                >
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- LOGO --}}
                <span class="font-semibold text-gray-800">
                    {{ config('app.name') }}
                </span>
            </div>

            {{-- RIGHT --}}
            <a href="{{ route('profile') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="hidden sm:block text-sm text-gray-700">
                    {{ Auth::user()->name }}
                </span>
            </a>

        </div>
    </div>
</nav>
