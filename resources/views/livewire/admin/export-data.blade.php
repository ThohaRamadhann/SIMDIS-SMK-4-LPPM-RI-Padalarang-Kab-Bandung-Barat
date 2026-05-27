<div class="space-y-2">

    {{-- Info --}}
    <p class="text-[10px] text-gray-400">
        Download template kosong atau export semua data existing ke Excel.
    </p>

    {{-- Tombol-tombol --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Template --}}
        <button wire:click="exportAs('template')"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-70 cursor-not-allowed"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                   bg-emerald-600 hover:bg-emerald-700 text-white">
            <span wire:loading wire:target="exportAs('template')">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="exportAs('template')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="exportAs('template')">Download Template</span>
            <span wire:loading wire:target="exportAs('template')">Menyiapkan...</span>
        </button>

        {{-- Export Data --}}
        <button wire:click="exportAs('data')"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-70 cursor-not-allowed"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                   bg-[#0D2D6B] hover:bg-[#163580] text-white">
            <span wire:loading wire:target="exportAs('data')">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="exportAs('data')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="exportAs('data')">Export Data</span>
            <span wire:loading wire:target="exportAs('data')">Menyiapkan...</span>
        </button>

    </div>
</div>