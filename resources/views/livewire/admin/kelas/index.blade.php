<div class="space-y-3">

    {{-- ── FLASH MESSAGE ── --}}
    @if (session()->has('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200
                    text-green-700 px-3 py-2 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4
                        {{ $editingId ? 'border-[rgba(245,184,0,0.4)] ring-2 ring-[rgba(245,184,0,0.12)]' : '' }}"
                 style="transition: box-shadow .25s, border-color .25s;">

                {{-- Banner edit --}}
                @if ($editingId)
                    <div class="-mx-4 -mt-4 mb-3 px-4 py-2 rounded-t-2xl text-[11px] font-semibold"
                         style="background:rgba(245,184,0,0.1);border-bottom:1px solid rgba(245,184,0,0.3);color:#7a5c00;">
                        <i class="fas fa-pen-to-square mr-1"></i> Mode Edit Kelas
                    </div>
                @endif

                <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
                </h3>
                <p class="text-xs text-gray-500 mb-3">Kelola data kelas SIMDIS</p>

                <div class="space-y-2">

                    {{-- Nama Kelas --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Nama Kelas *</label>
                        <input type="text" wire:model.defer="nama_kelas"
                            placeholder="Contoh: X TJA 1"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                        @error('nama_kelas')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tingkat & Jurusan (2 kolom) --}}
                    <div class="grid grid-cols-2 gap-2">

                        {{-- Tingkat: X / XI / XII --}}
                        <div>
                            <label class="text-xs font-semibold text-[#0D2D6B]">Tingkat *</label>
                            <select wire:model.defer="tingkat"
                                class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                       focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                                <option value="">-- Pilih --</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                            @error('tingkat')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Jurusan --}}
                        <div>
                            <label class="text-xs font-semibold text-[#0D2D6B]">Jurusan *</label>
                            <select wire:model.defer="jurusan"
                                class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                       focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                                <option value="">-- Pilih --</option>
                                <option value="Teknik Jaringan Akses">Teknik Jaringan Akses</option>
                                <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                <option value="Perhotelan">Perhotelan</option>
                                <option value="Otomotif">Otomotif</option>
                            </select>
                            @error('jurusan')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- Tahun Ajaran --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Tahun Ajaran *</label>
                        <input type="text" wire:model.defer="tahun_ajaran"
                            placeholder="Contoh: 2024/2025"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                        @error('tahun_ajaran')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Wali Kelas: Alpine.js search (sama polanya dengan wali murid) --}}
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: @entangle('id_walikelas'),

                        get filteredWali() {
                            if (this.search === '') return this.walis
                            return this.walis.filter(w =>
                                w.name.toLowerCase().includes(this.search.toLowerCase())
                            )
                        },

                        walis: [
                            @foreach($waliKelasList as $w)
                            {
                                id: {{ $w->id_walikelas }},
                                name: '{{ addslashes(optional($w->pengguna)->name ?? '-') }}'
                            },
                            @endforeach
                        ],

                        selectedName() {
                            if (!this.selected) return '-- Pilih Wali Kelas (Opsional) --'
                            const found = this.walis.find(w => w.id == this.selected)
                            return found ? found.name : '-- Pilih Wali Kelas (Opsional) --'
                        }
                    }" class="relative">

                        <label class="text-xs font-semibold text-[#0D2D6B]">Wali Kelas</label>

                        {{-- Trigger button --}}
                        <button type="button" @click="open = !open"
                            class="mt-0.5 w-full h-10 px-3 rounded-lg border border-gray-200
                                   bg-gray-50 text-left text-sm flex items-center justify-between
                                   hover:bg-white focus:border-[#F5B800] transition">
                            <span class="truncate"
                                :class="selected ? 'text-gray-700' : 'text-gray-400'"
                                x-text="selectedName()">
                            </span>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                {{-- Tombol clear jika sudah dipilih --}}
                                <span x-show="selected"
                                    @click.stop="selected = ''; search = ''; open = false"
                                    class="text-gray-300 hover:text-red-400 transition cursor-pointer p-0.5 rounded">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute z-50 mt-1 w-full bg-white border border-gray-200
                                   rounded-xl shadow-xl overflow-hidden"
                            style="display:none">

                            {{-- Search input --}}
                            <div class="p-2 border-b border-gray-100">
                                <input type="text" x-model="search"
                                    placeholder="Cari nama wali kelas..."
                                    @click.stop
                                    class="w-full h-9 px-3 text-sm rounded-lg border border-gray-200
                                           bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                            </div>

                            {{-- List --}}
                            <div class="max-h-52 overflow-y-auto">

                                {{-- Opsi kosongkan --}}
                                <button type="button"
                                    @click="selected = ''; open = false; search = ''"
                                    class="w-full px-3 py-2.5 text-left text-sm text-gray-400 italic
                                           hover:bg-[#F0F4FB] transition border-b border-gray-50">
                                    Tidak ada wali kelas
                                </button>

                                <template x-if="filteredWali.length === 0">
                                    <div class="px-3 py-4 text-sm text-gray-400 text-center">
                                        <i class="fas fa-user-slash block text-lg mb-1 opacity-30"></i>
                                        Wali kelas tidak ditemukan
                                    </div>
                                </template>

                                <template x-for="wali in filteredWali" :key="wali.id">
                                    <button type="button"
                                        @click="selected = wali.id; open = false; search = ''"
                                        class="w-full px-3 py-2.5 text-left text-sm
                                               hover:bg-[#F0F4FB] transition border-b border-gray-50
                                               flex items-center gap-2">
                                        {{-- Avatar inisial --}}
                                        <div style="width:26px;height:26px;border-radius:50%;flex-shrink:0;
                                                    background:linear-gradient(135deg,#0D2D6B,#163580);
                                                    color:#F5B800;font-size:9px;font-weight:700;
                                                    display:flex;align-items:center;justify-content:center;"
                                            x-text="wali.name.charAt(0).toUpperCase()">
                                        </div>
                                        <span x-text="wali.name" class="text-[#0D2D6B] font-medium"></span>
                                    </button>
                                </template>

                            </div>
                        </div>

                    </div>

                    {{-- Info mode edit --}}
                    @if ($editingId)
                        <div class="text-xs px-3 py-2 rounded-lg border bg-amber-50 text-amber-700 border-amber-100">
                            <i class="fas fa-pen-to-square mr-1"></i>
                            Mode Edit: mengubah data kelas yang sudah ada
                        </div>
                    @endif

                    {{-- Buttons --}}
                    <div class="flex gap-2 pt-2">
                        <button wire:click="save"
                            class="bg-[#0D2D6B] text-white px-4 py-2 text-sm font-semibold rounded-lg
                                   hover:bg-[#163580] transition">
                            <i class="fas fa-{{ $editingId ? 'save' : 'plus' }} mr-1"></i>
                            {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                        <button wire:click="resetForm"
                            class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm
                                   font-semibold rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-xmark mr-1"></i>
                            {{ $editingId ? 'Batal' : 'Reset' }}
                        </button>
                    </div>

                </div>
            </div>
        </div>


        {{-- ================= TABLE ================= --}}
        <div class="xl:col-span-3">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

                {{-- Header --}}
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                            {{ $showTrash ? 'Tong Sampah' : 'Daftar Kelas' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $showTrash ? 'Data kelas yang telah dihapus.' : 'Data seluruh kelas SIMDIS' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-[#0D2D6B] bg-blue-50 px-3 py-1 rounded-full">
                            {{ $kelas->total() }} data
                        </span>
                        <button wire:click="$toggle('showTrash')"
                            class="flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                                   {{ $showTrash
                                       ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'
                                       : 'bg-[#F0F4FB] text-[#4A5E8A] border-[#E2EAF4] hover:bg-[#e2eaf7]' }}">
                            <i class="fas {{ $showTrash ? 'fa-arrow-left' : 'fa-trash-can' }}"></i>
                            {{ $showTrash ? 'Kembali' : 'Sampah' }}
                            @if (!$showTrash && $trashCount > 0)
                                <span class="ml-1 bg-red-500 text-white text-[10px] font-bold
                                             px-1.5 py-0.5 rounded-full leading-none">
                                    {{ $trashCount }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>

                {{-- ── TOOLBAR ── --}}
                @if (!$showTrash)
                    <div class="space-y-2 mb-3">

                        {{-- Row 1: Search + Sort + Per Page --}}
                        <div class="flex flex-wrap gap-2">

                            {{-- Search --}}
                            <div class="relative flex-1 min-w-[150px]">
                                <input type="text"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Cari nama kelas..."
                                    class="w-full h-9 pl-8 pr-8 text-xs rounded-lg border border-gray-200
                                           bg-gray-50 focus:bg-white focus:border-[#F5B800]
                                           focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2
                                          text-gray-400 text-xs pointer-events-none"></i>
                                @if ($search)
                                    <button wire:click="$set('search', '')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-xmark text-xs"></i>
                                    </button>
                                @endif
                            </div>

                            {{-- Sort --}}
                            <select wire:model.live="sortBy"
                                class="h-9 px-2 text-xs rounded-lg border border-gray-200
                                       bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                                <option value="terbaru">Terbaru</option>
                                <option value="az">A → Z</option>
                                <option value="za">Z → A</option>
                            </select>

                            {{-- Per page --}}
                            <select wire:model.live="perPage"
                                class="h-9 px-2 text-xs rounded-lg border border-gray-200
                                       bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition w-16">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>

                        </div>

                        {{-- Row 2: Filter chips --}}
                        <div class="flex flex-wrap gap-2 items-center">

                            {{-- Filter Tingkat --}}
                            <select wire:model.live="filterTingkat"
                                class="h-8 px-2 text-xs rounded-lg border outline-none transition
                                       {{ $filterTingkat
                                           ? 'bg-[#0D2D6B] text-white border-[#0D2D6B]'
                                           : 'bg-gray-50 border-gray-200 text-gray-600 focus:border-[#F5B800]' }}">
                                <option value="">Semua Tingkat</option>
                                @foreach ($tingkatOptions as $t)
                                    <option value="{{ $t }}" {{ $filterTingkat === $t ? 'selected' : '' }}>
                                        Tingkat {{ $t }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Jurusan --}}
                            <select wire:model.live="filterJurusan"
                                class="h-8 px-2 text-xs rounded-lg border outline-none transition
                                       {{ $filterJurusan
                                           ? 'bg-[#0D2D6B] text-white border-[#0D2D6B]'
                                           : 'bg-gray-50 border-gray-200 text-gray-600 focus:border-[#F5B800]' }}">
                                <option value="">Semua Jurusan</option>
                                @foreach ($jurusanOptions as $j)
                                    <option value="{{ $j }}" {{ $filterJurusan === $j ? 'selected' : '' }}>
                                        {{ $j }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Tahun Ajaran --}}
                            <select wire:model.live="filterTahun"
                                class="h-8 px-2 text-xs rounded-lg border outline-none transition
                                       {{ $filterTahun
                                           ? 'bg-[#0D2D6B] text-white border-[#0D2D6B]'
                                           : 'bg-gray-50 border-gray-200 text-gray-600 focus:border-[#F5B800]' }}">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunOptions as $ta)
                                    <option value="{{ $ta }}" {{ $filterTahun === $ta ? 'selected' : '' }}>
                                        {{ $ta }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Status Wali Kelas --}}
                            <select wire:model.live="filterWali"
                                class="h-8 px-2 text-xs rounded-lg border outline-none transition
                                       {{ $filterWali
                                           ? 'bg-[#0D2D6B] text-white border-[#0D2D6B]'
                                           : 'bg-gray-50 border-gray-200 text-gray-600 focus:border-[#F5B800]' }}">
                                <option value="">Semua Wali</option>
                                <option value="ada">Ada Wali Kelas</option>
                                <option value="kosong">Belum Ada Wali</option>
                            </select>

                            {{-- Tombol Reset Filter --}}
                            @if ($hasActiveFilters)
                                <button wire:click="resetFilters"
                                    class="h-8 px-3 text-xs font-semibold rounded-lg border
                                           bg-red-50 text-red-600 border-red-200 hover:bg-red-100 transition-colors
                                           flex items-center gap-1">
                                    <i class="fas fa-filter-circle-xmark"></i> Reset Filter
                                </button>
                            @endif

                        </div>

                    </div>
                @else
                    @if ($trashCount > 0)
                        <div class="flex justify-end mb-3">
                            <button wire:click="emptyTrash"
                                wire:confirm="Hapus SEMUA data di tong sampah secara permanen?"
                                class="flex items-center gap-1 text-xs font-semibold px-3 py-1.5
                                       bg-red-50 text-red-600 border border-red-200 rounded-lg
                                       hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash"></i> Kosongkan Semua
                            </button>
                        </div>
                    @endif
                @endif

                {{-- ── TABLE ── --}}
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                            <tr>
                                <th class="px-3 py-2 text-left font-bold">No</th>
                                <th class="px-3 py-2 text-left font-bold">Nama Kelas</th>
                                <th class="px-3 py-2 text-left font-bold">Tingkat</th>
                                <th class="px-3 py-2 text-left font-bold">Jurusan</th>
                                <th class="px-3 py-2 text-left font-bold">Tahun</th>
                                <th class="px-3 py-2 text-left font-bold">Wali Kelas</th>
                                <th class="px-3 py-2 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($kelas as $k)
                                @php $isEditingRow = $editingId == $k->id_kelas; @endphp
                                <tr wire:key="kelas-{{ $k->id_kelas }}"
                                    class="hover:bg-gray-50 transition
                                           {{ $isEditingRow ? 'bg-[rgba(245,184,0,0.07)]' : '' }}"
                                    style="{{ $isEditingRow ? 'outline:1.5px solid rgba(245,184,0,0.35);outline-offset:-1px;' : '' }}">

                                    {{-- No --}}
                                    <td class="px-3 py-2 text-gray-400 text-xs">
                                        {{ $kelas->firstItem() + $loop->index }}
                                    </td>

                                    {{-- Nama Kelas --}}
                                    <td class="px-3 py-2">
                                        <span class="font-semibold text-[#0D2D6B] text-xs">
                                            {{ $k->nama_kelas }}
                                        </span>
                                        @if ($k->trashed())
                                            <span style="font-size:10px;color:#DC2626;background:rgba(229,62,62,0.08);
                                                         padding:1px 6px;border-radius:20px;margin-left:4px;font-weight:600;">
                                                Dihapus
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Tingkat --}}
                                    <td class="px-3 py-2">
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-purple-50 text-purple-600">
                                            {{ $k->tingkat }}
                                        </span>
                                    </td>

                                    {{-- Jurusan --}}
                                    <td class="px-3 py-2">
                                        <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-50 text-blue-600">
                                            {{ $k->jurusan }}
                                        </span>
                                    </td>

                                    {{-- Tahun --}}
                                    <td class="px-3 py-2 text-gray-500 text-xs">
                                        {{ $k->tahun_ajaran }}
                                    </td>

                                    {{-- Wali Kelas --}}
                                    <td class="px-3 py-2">
                                        @if (optional($k->waliKelas)->pengguna)
                                            <div class="flex items-center gap-1.5">
                                                <div style="width:20px;height:20px;border-radius:50%;flex-shrink:0;
                                                            background:linear-gradient(135deg,#0D2D6B,#163580);
                                                            color:#F5B800;font-size:9px;font-weight:700;
                                                            display:flex;align-items:center;justify-content:center;">
                                                    {{ strtoupper(substr($k->waliKelas->pengguna->name, 0, 1)) }}
                                                </div>
                                                <span class="text-xs text-[#0D2D6B] font-medium">
                                                    {{ $k->waliKelas->pengguna->name }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-red-50 text-red-400">
                                                Belum ada
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                        @if ($showTrash)
                                            <button wire:click="restore({{ $k->id_kelas }})"
                                                class="text-xs font-semibold mr-2 transition"
                                                style="color:#276749;">
                                                <i class="fas fa-rotate-left"></i> Pulihkan
                                            </button>
                                            <button wire:click="forceDelete({{ $k->id_kelas }})"
                                                wire:confirm="Hapus permanen? Data tidak bisa dikembalikan."
                                                class="text-red-500 text-xs font-semibold hover:text-red-700 transition">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        @else
                                            <button wire:click="edit({{ $k->id_kelas }})"
                                                class="text-[#0D2D6B] text-xs font-semibold hover:text-[#163580] mr-2 transition">
                                                {{ $isEditingRow ? '✎ Diedit' : 'Edit' }}
                                            </button>
                                            @if (!$isEditingRow)
                                                <button wire:click="hapus({{ $k->id_kelas }})"
                                                    wire:confirm="Pindahkan kelas ini ke tong sampah?"
                                                    class="text-red-500 text-xs font-semibold hover:text-red-700 transition">
                                                    Hapus
                                                </button>
                                            @endif
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-400 text-xs">
                                        <i class="fas fa-{{ $showTrash ? 'trash' : 'school' }} block text-2xl mb-2 opacity-25"></i>
                                        {{ $showTrash ? 'Tong sampah kosong.' : 'Tidak ada data kelas ditemukan.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ── PAGINATION ── --}}
                @if ($kelas->hasPages())
                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs text-[#4A5E8A]">
                            Menampilkan {{ $kelas->firstItem() }}–{{ $kelas->lastItem() }}
                            dari {{ $kelas->total() }} data
                        </span>
                        <div class="flex items-center gap-1">

                            @if ($kelas->onFirstPage())
                                <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <button wire:click="previousPage" class="simdis-page-btn">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                            @endif

                            @foreach ($kelas->getUrlRange(
                                max(1, $kelas->currentPage() - 2),
                                min($kelas->lastPage(), $kelas->currentPage() + 2)
                            ) as $page => $url)
                                @if ($page == $kelas->currentPage())
                                    <span class="simdis-page-btn simdis-page-active">{{ $page }}</span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" class="simdis-page-btn">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            @if ($kelas->hasMorePages())
                                <button wire:click="nextPage" class="simdis-page-btn">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            @else
                                <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            @endif

                        </div>
                    </div>
                @else
                    <div class="mt-2 text-[11px] text-gray-400">
                        Total {{ $kelas->total() }} data
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>