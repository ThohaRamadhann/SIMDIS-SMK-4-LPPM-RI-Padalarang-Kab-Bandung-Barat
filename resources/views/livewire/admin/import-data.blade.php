<div class="space-y-2">

    {{-- ── STEP 1: Upload Area (sembunyikan kalau sudah preview) ── --}}
    @if (!$previewing)
        <div x-data="{ dragging: false }"
            x-on:dragover.prevent="dragging = true"
            x-on:dragleave.prevent="dragging = false"
            x-on:drop.prevent="dragging = false"
            :class="dragging ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50'"
            class="relative flex items-center justify-center w-full h-16 border-2 border-dashed
                   rounded-xl cursor-pointer transition-colors hover:border-blue-400 hover:bg-blue-50">

            <label for="import-file-{{ $type }}"
                class="flex items-center gap-2 cursor-pointer w-full h-full px-3 justify-center">
                @if ($file)
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-green-600 truncate">{{ $file->getClientOriginalName() }}</p>
                        <p class="text-[10px] text-gray-400">Klik untuk ganti file</p>
                    </div>
                @else
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <div>
                        <p class="text-xs font-medium text-gray-600">Seret & lepas atau klik untuk memilih</p>
                        <p class="text-[10px] text-gray-400">.xlsx, .xls, .csv — Maks. 5MB</p>
                    </div>
                @endif
            </label>

            <input id="import-file-{{ $type }}" type="file" wire:model="file"
                accept=".xlsx,.xls,.csv"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"/>
        </div>

        @error('file')
            <p class="text-xs text-red-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"/>
                </svg>
                {{ $message }}
            </p>
        @enderror

        {{-- Tombol Preview --}}
        <div class="flex items-center gap-2">
            <button wire:click="preview"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-not-allowed"
                @if (!$file) disabled @endif
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                       bg-[#0D2D6B] hover:bg-[#163580] disabled:bg-gray-200 disabled:text-gray-400
                       disabled:cursor-not-allowed text-white">
                <span wire:loading wire:target="preview">
                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="preview">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="preview">Preview & Validasi</span>
                <span wire:loading wire:target="preview">Membaca file...</span>
            </button>

            @if ($file)
                <button wire:click="reset_form"
                    class="px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700
                           hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </button>
            @endif
        </div>
    @endif

    {{-- ── STEP 2: Preview Tabel ── --}}
    @if ($previewing && count($previewRows) > 0)
        <div class="space-y-2">

            {{-- Header preview --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#0D2D6B]">
                        Preview: {{ count($previewRows) }} baris ditemukan
                    </span>
                    @if (count($previewErrors) > 0)
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-600">
                            {{ count($previewErrors) }} baris bermasalah
                        </span>
                    @else
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-600">
                            Semua baris valid
                        </span>
                    @endif
                </div>
                <button wire:click="reset_form"
                    class="text-[10px] text-gray-400 hover:text-gray-600 transition-colors">
                    ✕ Batal
                </button>
            </div>

            {{-- Tabel preview --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200 max-h-64 overflow-y-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-2 py-1.5 text-left font-bold text-gray-500 w-8">#</th>
                            <th class="px-2 py-1.5 text-left font-bold text-gray-500 w-8">Status</th>
                            @foreach (array_keys($previewRows[0]) as $col)
                                <th class="px-2 py-1.5 text-left font-bold text-[#0D2D6B] whitespace-nowrap">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($previewRows as $index => $row)
                            @php $hasError = isset($previewErrors[$index]); @endphp
                            <tr class="{{ $hasError ? 'bg-red-50' : 'bg-white hover:bg-gray-50' }} transition-colors">
                                <td class="px-2 py-1.5 text-gray-400">{{ $index + 2 }}</td>
                                <td class="px-2 py-1.5">
                                    @if ($hasError)
                                        <span title="{{ implode(', ', $previewErrors[$index]) }}"
                                            class="cursor-help inline-flex items-center gap-1 text-red-500 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"/>
                                            </svg>
                                            Error
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-green-500 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"/>
                                            </svg>
                                            OK
                                        </span>
                                    @endif
                                </td>
                                @foreach ($row as $val)
                                    <td class="px-2 py-1.5 {{ $hasError ? 'text-red-700' : 'text-gray-700' }} whitespace-nowrap">
                                        {{ $val ?? '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Detail error --}}
            @if (count($previewErrors) > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl p-3 space-y-1">
                    <p class="text-xs font-bold text-red-600 mb-1">Detail masalah yang ditemukan:</p>
                    @foreach ($previewErrors as $index => $errors)
                        <div class="text-xs text-red-600">
                            <span class="font-semibold">Baris {{ $index + 2 }}:</span>
                            {{ implode(', ', $errors) }}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tombol aksi --}}
            <div class="flex items-center gap-2 pt-1">
                {{-- Import tetap bisa jalan meski ada error (baris error akan diskip) --}}
                <button wire:click="import"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                           {{ count($previewErrors) > 0
                               ? 'bg-yellow-500 hover:bg-yellow-600'
                               : 'bg-blue-600 hover:bg-blue-700' }} text-white">
                    <span wire:loading wire:target="import">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="import">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="import">
                        {{ count($previewErrors) > 0 ? 'Import (lewati baris error)' : 'Import Sekarang' }}
                    </span>
                    <span wire:loading wire:target="import">Memproses...</span>
                </button>

                <button wire:click="reset_form"
                    class="px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700
                           hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </div>
    @endif

    {{-- ── STEP 3: Hasil Import ── --}}
    @if ($done)
        <div class="rounded-xl border text-xs
            {{ count($importErrors) > 0 ? 'border-yellow-200 bg-yellow-50' : 'border-green-200 bg-green-50' }} p-3">

            <div class="flex items-center gap-1.5 mb-1">
                @if (count($importErrors) > 0)
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold text-yellow-700">Selesai dengan peringatan</span>
                @else
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold text-green-700">Import berhasil!</span>
                @endif
            </div>

            <p class="{{ count($importErrors) > 0 ? 'text-yellow-700' : 'text-green-700' }}">
                <strong>{{ $imported }}</strong> data berhasil diimport.
                @if (count($importErrors) > 0)
                    <strong>{{ count($importErrors) }}</strong> baris dilewati.
                @endif
            </p>

            @if (count($importErrors) > 0)
                <details class="mt-2">
                    <summary class="text-yellow-700 cursor-pointer hover:text-yellow-900 font-medium">
                        Lihat detail error ({{ count($importErrors) }} item)
                    </summary>
                    <ul class="mt-1.5 space-y-1 max-h-36 overflow-y-auto">
                        @foreach ($importErrors as $error)
                            <li class="text-red-600 bg-red-50 rounded px-2 py-1 border border-red-200">
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif

            <button wire:click="reset_form"
                class="mt-2 text-[10px] font-semibold text-gray-500 hover:text-gray-700 underline">
                Import lagi
            </button>
        </div>
    @endif

</div>