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
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- LOGO SIMDIS --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img
                        src="{{ asset('images/logo_simdis.png') }}"
                        alt="Logo SIMDIS"
                        class="h-9 w-auto object-contain"
                    >
                </a>

            </div>

            {{-- RIGHT --}}
            <a href="{{ route('profile') }}"
               class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-[#F0F4FB] transition-colors duration-200 group">

                {{-- Avatar inisial dengan warna navy --}}
                <div class="w-9 h-9 rounded-full bg-[#0D2D6B] flex items-center justify-center
                            text-white font-bold text-sm shadow-sm
                            group-hover:bg-[#163580] transition-colors duration-200">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <span class="hidden sm:block text-sm font-medium text-[#0D2D6B]">
                    {{ Auth::user()->name }}
                </span>

                {{-- Chevron kecil sebagai hint klik --}}
                <svg class="hidden sm:block w-4 h-4 text-[#4A5E8A] group-hover:text-[#0D2D6B] transition-colors duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>

            </a>

        </div>
    </div>
</nav>