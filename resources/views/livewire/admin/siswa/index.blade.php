<div x-data="{ formOpen: false }" @open-form.window="formOpen = true" class="space-y-2">

    {{-- SUCCESS NOTIFICATION --}}
    @if (session()->has('success'))
        <div
            class="flex items-center gap-3 bg-green-50 border border-green-200
                   text-green-700 px-3 py-2 rounded-xl shadow-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ================= FORM ACCORDION ================= --}}
    <div x-show="formOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" x-cloak>
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-3">

            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-[16px] font-bold text-[#0D2D6B]">
                        {{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}
                    </h2>
                    <p class="text-xs text-gray-500">Kelola data siswa SIMDIS</p>
                </div>
                <button @click="formOpen = false; $wire.resetForm()"
                    class="flex items-center justify-center w-7 h-7 rounded-lg
                           bg-gray-100 hover:bg-gray-200 text-gray-400 hover:text-gray-600
                           transition-colors flex-shrink-0"
                    title="Tutup form">
                    <i class="fas fa-xmark text-xs"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Nama Siswa *</label>
                    <input type="text" wire:model="nama" placeholder="Masukkan nama siswa"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                    @error('nama')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">NIS *</label>
                    <input type="text" wire:model="nis" placeholder="Nomor Induk Siswa"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                    @error('nis')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Kelas *</label>
                    <select wire:model="id_kelas"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('id_kelas')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Searchable dropdown Wali Siswa --}}
                <div x-data="{
                    open: false,
                    search: '',
                    selected: @entangle('id_walisiswa'),
                    get filteredWali() {
                        return this.walis.filter(w =>
                            w.name.toLowerCase().includes(this.search.toLowerCase())
                        )
                    },
                    walis: [
                        @foreach($wali as $w) {
                            id: {{ $w->id_walisiswa }},
                            name: '{{ optional($w->pengguna)->name ?? '-' }} {{ $w->hubungan ? '(' . $w->hubungan . ')' : '' }}'
                        },
                        @endforeach
                    ],
                    selectedName() {
                        const found = this.walis.find(w => w.id == this.selected)
                        return found ? found.name : '-- Pilih Wali Siswa --'
                    }
                }" class="relative">
                    <label class="text-xs font-semibold text-[#0D2D6B]">Wali Siswa *</label>
                    <button type="button" @click="open = !open"
                        class="mt-0.5 w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50
                               text-left text-sm flex items-center justify-between hover:bg-white
                               focus:border-[#F5B800] transition">
                        <span class="truncate" :class="selected ? 'text-gray-700' : 'text-gray-400'"
                            x-text="selectedName()"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
                        style="display:none">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" x-model="search" placeholder="Cari nama wali siswa..."
                                class="w-full h-9 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                       focus:bg-white focus:border-[#F5B800] outline-none transition">
                        </div>
                        <div class="max-h-56 overflow-y-auto">
                            <template x-if="filteredWali.length === 0">
                                <div class="px-3 py-4 text-sm text-gray-400 text-center">Wali siswa tidak ditemukan
                                </div>
                            </template>
                            <template x-for="wali in filteredWali" :key="wali.id">
                                <button type="button" @click="selected = wali.id; open = false; search = '';"
                                    class="w-full px-3 py-2.5 text-left text-sm hover:bg-[#F0F4FB] transition border-b border-gray-50">
                                    <span x-text="wali.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('id_walisiswa')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Status</label>
                    <select wire:model="status"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

            </div>

            {{-- BUTTON + CATATAN --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-3 mt-3 border-t border-gray-100">
                <div class="flex gap-2">
                    @if ($isEdit)
                        <button wire:click="update"
                            class="bg-[#0D2D6B] text-white px-4 py-2 text-sm font-semibold rounded-lg
                                   hover:bg-[#163580] transition">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <button wire:click="resetForm" @click="formOpen = false"
                            class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm
                                   font-semibold rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-xmark mr-1"></i> Batal
                        </button>
                    @else
                        <button wire:click="store"
                            class="bg-[#0D2D6B] text-white px-4 py-2 text-sm font-semibold rounded-lg
                                   hover:bg-[#163580] transition">
                            <i class="fas fa-plus mr-1"></i> Simpan
                        </button>
                        <button type="button" @click="formOpen = false; $wire.resetForm()"
                            class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm
                                   font-semibold rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-xmark mr-1"></i> Batal
                        </button>
                    @endif
                </div>
                <ul class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#4A5E8A]">
                    <li><span class="text-[#F5B800] font-bold">•</span> Pastikan wali siswa sudah terdaftar</li>
                    <li><span class="text-[#F5B800] font-bold">•</span> NIS harus unik per siswa</li>
                    <li><span class="text-[#F5B800] font-bold">•</span> Status nonaktif menonaktifkan siswa</li>
                </ul>
            </div>

        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-3">
            <div>
                <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $showTrash ? 'Tong Sampah' : 'Daftar Siswa' }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ $showTrash ? 'Siswa yang telah dihapus.' : 'Data siswa aktif & nonaktif' }}
                </p>
            </div>
            <div class="flex flex-wrap justify-end items-center gap-2">
                <span class="text-xs font-semibold text-[#0D2D6B] bg-blue-50 px-3 py-1 rounded-full">
                    {{ $dataSiswa->total() }} siswa
                </span>
                @if (!$showTrash)
                    <button @click="formOpen = !formOpen; if(!formOpen) $wire.resetForm()"
                        class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors"
                        :class="formOpen
                            ?
                            'bg-[#F0F4FB] text-[#4A5E8A] border-[#E2EAF4] hover:bg-[#e2eaf7]' :
                            'bg-[#0D2D6B] text-white border-[#0D2D6B] hover:bg-[#163580]'">
                        <i class="fas text-xs" :class="formOpen ? 'fa-xmark' : 'fa-plus'"></i>
                        <span x-text="formOpen ? 'Tutup Form' : 'Tambah Siswa'"></span>
                    </button>
                @endif
                <button wire:click="$toggle('showTrash')"
                    class="flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                           {{ $showTrash
                               ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'
                               : 'bg-[#F0F4FB] text-[#4A5E8A] border-[#E2EAF4] hover:bg-[#e2eaf7]' }}">
                    <i class="fas {{ $showTrash ? 'fa-arrow-left' : 'fa-trash-can' }}"></i>
                    {{ $showTrash ? 'Kembali' : 'Tong Sampah' }}
                    @if (!$showTrash && $trashCount > 0)
                        <span
                            class="ml-1 bg-red-500 text-white text-[10px] font-bold
                                     px-1.5 py-0.5 rounded-full leading-none">
                            {{ $trashCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        {{-- ── TOOLBAR ── --}}
        @if (!$showTrash)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 p-3 bg-gray-50 border border-gray-100 rounded-xl">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import Data
                    </p>
                    @livewire('admin.import-data', ['type' => 'siswa'])
                </div>
                <div class="sm:border-l sm:border-gray-200 sm:pl-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Data
                    </p>
                    @livewire('admin.export-data', [
                        'type' => 'siswa',
                        'filterTahunAjaran' => $filterTahunAjaran,
                        'filterKelas' => $filterKelas,
                        'filterStatus' => $filterStatus,
                        'search' => $search,
                    ])
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-3">
                <div class="col-span-2 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIS..."
                        class="w-full h-9 pl-8 pr-8 text-xs rounded-lg border border-gray-200
                               bg-gray-50 focus:bg-white focus:border-[#F5B800]
                               focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    @if ($search)
                        <button wire:click="$set('search','')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-xmark text-xs"></i>
                        </button>
                    @endif
                </div>
                <div>
                    <select wire:model.live="filterKelas"
                        class="w-full h-9 px-2 text-xs rounded-lg border border-gray-200
           bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                        <option value="">Semua Kelas</option>
                        @foreach ($allKelas->groupBy('tingkat') as $tingkat => $kelasList)
                            <optgroup label="Tingkat {{ $tingkat }}">
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="filterTahunAjaran"
                        class="w-full h-9 px-2 text-xs rounded-lg border border-gray-200
                               bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach ($tahunAjaranList as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="filterStatus"
                        class="w-full h-9 px-2 text-xs rounded-lg border border-gray-200
                               bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="col-span-2 lg:col-span-4 flex gap-2 justify-end">
                    <select wire:model.live="sortBy"
                        class="h-9 px-2 text-xs rounded-lg border border-gray-200
                               bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                        <option value="az">A → Z</option>
                        <option value="za">Z → A</option>
                        <option value="terbaru">Terbaru</option>
                    </select>
                    <select wire:model.live="perPage"
                        class="h-9 px-2 text-xs rounded-lg border border-gray-200
                               bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition w-16">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        @else
            @if ($trashCount > 0)
                <div class="flex justify-end mb-3">
                    <button wire:click="emptyTrash"
                        wire:confirm="Hapus SEMUA siswa di tong sampah secara permanen? Tidak bisa dikembalikan."
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
                        <th class="px-3 py-2 text-left font-bold">Nama</th>
                        <th class="px-3 py-2 text-left font-bold">NIS</th>
                        <th class="px-3 py-2 text-left font-bold">Kelas</th>
                        <th class="px-3 py-2 text-left font-bold">Wali Siswa</th>
                        <th class="px-3 py-2 text-left font-bold">Status</th>
                        <th class="px-3 py-2 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($dataSiswa as $s)
                        @php $isEditingRow = $isEdit && $id_siswa == $s->id_siswa; @endphp
                        <tr wire:key="siswa-{{ $s->id_siswa }}"
                            class="hover:bg-gray-50 transition
                                   {{ $isEditingRow ? 'bg-[rgba(245,184,0,0.07)] outline outline-[1.5px] outline-[rgba(245,184,0,0.35)]' : '' }}">

                            <td class="px-3 py-2 text-gray-400 text-xs">
                                {{ $dataSiswa->firstItem() + $loop->index }}
                            </td>

                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <div
                                        style="width:24px;height:24px;border-radius:50%;flex-shrink:0;
                                                background:linear-gradient(135deg,#0D2D6B,#163580);
                                                color:#F5B800;font-size:10px;font-weight:700;
                                                display:flex;align-items:center;justify-content:center;">
                                        {{ strtoupper(substr($s->nama, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-[#0D2D6B] text-xs">
                                        {{ $s->nama }}
                                        @if ($s->trashed())
                                            <span
                                                style="font-size:10px;color:#DC2626;background:rgba(229,62,62,0.08);
                                                         padding:1px 6px;border-radius:20px;margin-left:4px;font-weight:600;">
                                                Dihapus
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <td class="px-3 py-2 text-gray-500 text-xs">{{ $s->nis }}</td>

                            <td class="px-3 py-2 text-gray-600 text-xs">
                                @if ($s->kelas)
                                    <div class="flex flex-col">
                                        <span class="font-medium text-[#0D2D6B]">{{ $s->kelas->nama_kelas }}</span>
                                        <span class="text-[11px] text-gray-700">
                                            {{ $s->kelas->tingkat }} {{ $s->kelas->jurusan }} •
                                            {{ $s->kelas->tahun_ajaran }}
                                        </span>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- ← ganti waliMurid → waliSiswa --}}
                            <td class="px-3 py-2 text-gray-500 text-xs">
                                {{ optional(optional($s->waliSiswa)->pengguna)->name ?? '-' }}
                            </td>

                            <td class="px-3 py-2">
                                <span
                                    class="text-[10px] font-semibold px-2 py-1 rounded-full
                                    {{ $s->status === 'aktif' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @if ($showTrash)
                                    <button wire:click="restore({{ $s->id_siswa }})"
                                        class="text-xs font-semibold mr-2 transition" style="color:#276749;">
                                        <i class="fas fa-rotate-left"></i> Pulihkan
                                    </button>
                                    <button wire:click="forceDelete({{ $s->id_siswa }})"
                                        wire:confirm="Hapus permanen? Data tidak bisa dikembalikan."
                                        class="text-red-500 text-xs font-semibold hover:text-red-700 transition">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                @else
                                    <button wire:click="edit({{ $s->id_siswa }})" @click="$dispatch('open-form')"
                                        class="text-[#0D2D6B] text-xs font-semibold hover:text-[#163580] mr-2 transition">
                                        {{ $isEditingRow ? '✎ Diedit' : 'Edit' }}
                                    </button>
                                    @if (!$isEditingRow)
                                        <button wire:click="delete({{ $s->id_siswa }})"
                                            wire:confirm="Pindahkan siswa ini ke tong sampah?"
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
                                <i
                                    class="fas fa-{{ $showTrash ? 'trash' : 'user-graduate' }} block text-2xl mb-2 opacity-25"></i>
                                {{ $showTrash ? 'Tong sampah kosong.' : 'Tidak ada siswa ditemukan.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── PAGINATION ── --}}
        @if ($dataSiswa->hasPages())
            <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                <span class="text-xs text-[#4A5E8A]">
                    Menampilkan {{ $dataSiswa->firstItem() }}–{{ $dataSiswa->lastItem() }}
                    dari {{ $dataSiswa->total() }} siswa
                </span>
                <div class="flex items-center gap-1">
                    @if ($dataSiswa->onFirstPage())
                        <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <button wire:click="previousPage" class="simdis-page-btn">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                    @endif

                    @foreach ($dataSiswa->getUrlRange(max(1, $dataSiswa->currentPage() - 2), min($dataSiswa->lastPage(), $dataSiswa->currentPage() + 2)) as $page => $url)
                        @if ($page == $dataSiswa->currentPage())
                            <span class="simdis-page-btn simdis-page-active">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})"
                                class="simdis-page-btn">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($dataSiswa->hasMorePages())
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
                Total {{ $dataSiswa->total() }} siswa
            </div>
        @endif

    </div>

</div>
