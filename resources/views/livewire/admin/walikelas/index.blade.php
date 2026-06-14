<div x-data="{ formOpen: false }"
     @open-form.window="formOpen = true"
     class="space-y-3">

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200
                    text-green-700 px-3 py-2 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ================= FORM ACCORDION ================= --}}
    <div x-show="formOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         x-cloak>
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-[16px] font-bold text-[#0D2D6B]">
                        {{ ($isEdit ?? false) ? 'Edit Data Wali Kelas' : 'Tambah Data Wali Kelas' }}
                    </h2>
                    <p class="text-xs text-gray-500">Kelola data wali kelas SIMDIS</p>
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
                    <label class="text-xs font-semibold text-[#0D2D6B]">Nama Wali Kelas *</label>
                    <select wire:model="id_pengguna"
                        wire:change="$dispatch('updatedIdPengguna', $event.target.value)"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach ($pengguna as $p)
                            <option value="{{ $p->id_pengguna }}">{{ $p->name }} ({{ $p->username }})</option>
                        @endforeach
                    </select>
                    @error('id_pengguna') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    <p class="text-[11px] text-gray-400 mt-1">Pilih wali kelas dari pengguna terdaftar</p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">NUPTK (Opsional)</label>
                    <input type="text" wire:model.defer="nuptk" placeholder="Masukkan NUPTK jika ada"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                    @error('nuptk') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-[#0D2D6B]">Jabatan (Opsional)</label>
                    <input type="text" wire:model.defer="jabatan" placeholder="Contoh: Wali Kelas X IPA"
                        class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                    @error('jabatan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                @if ($id_pengguna)
                    <div class="sm:col-span-2 lg:col-span-3">
                        <div class="text-xs px-3 py-2 rounded-lg border
                                    {{ ($isEdit ?? false) ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-blue-50 text-blue-700 border-blue-100' }}">
                            <i class="fas fa-{{ ($isEdit ?? false) ? 'pen-to-square' : 'plus-circle' }} mr-1"></i>
                            {{ ($isEdit ?? false) ? 'Mode Edit: mengubah data yang sudah ada' : 'Mode Tambah: akan membuat data baru' }}
                        </div>
                    </div>
                @endif

            </div>

            <div class="flex gap-2 pt-3 mt-3 border-t border-gray-100">
                @if ($isEdit ?? false)
                    <button wire:click="update"
                        class="bg-[#0D2D6B] text-white px-4 py-2 text-sm font-semibold rounded-lg hover:bg-[#163580] transition">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                    <button wire:click="resetForm" @click="formOpen = false"
                        class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-xmark mr-1"></i> Batal
                    </button>
                @else
                    <button wire:click="store"
                        class="bg-[#0D2D6B] text-white px-4 py-2 text-sm font-semibold rounded-lg hover:bg-[#163580] transition">
                        <i class="fas fa-plus mr-1"></i> Simpan
                    </button>
                    <button type="button" @click="formOpen = false; $wire.resetForm()"
                        class="bg-white border border-gray-300 text-gray-600 px-4 py-2 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-xmark mr-1"></i> Batal
                    </button>
                @endif
            </div>

        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">

        <div class="flex justify-between items-center mb-3">
            <div>
                <h3 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $showTrash ? 'Tong Sampah' : 'Daftar Wali Kelas' }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ $showTrash ? 'Data wali kelas yang telah dihapus.' : 'Data seluruh wali kelas SIMDIS' }}
                </p>
            </div>
            <div class="flex flex-wrap justify-end items-center gap-2">
                <span class="text-xs font-semibold text-[#0D2D6B] bg-blue-50 px-3 py-1 rounded-full">
                    {{ $dataWK->total() }} data
                </span>
                @if (!$showTrash)
                    <button @click="formOpen = !formOpen; if(!formOpen) $wire.resetForm()"
                        class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors"
                        :class="formOpen
                            ? 'bg-[#F0F4FB] text-[#4A5E8A] border-[#E2EAF4] hover:bg-[#e2eaf7]'
                            : 'bg-[#0D2D6B] text-white border-[#0D2D6B] hover:bg-[#163580]'">
                        <i class="fas text-xs" :class="formOpen ? 'fa-xmark' : 'fa-plus'"></i>
                        <span x-text="formOpen ? 'Tutup Form' : 'Tambah Wali Kelas'"></span>
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
                        <span class="ml-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">
                            {{ $trashCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        @if (!$showTrash)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 p-3 bg-gray-50 border border-gray-100 rounded-xl">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Data
                    </p>
                    @livewire('admin.import-data', ['type' => 'wali_kelas'])
                </div>
                <div class="sm:border-l sm:border-gray-200 sm:pl-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1.5">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Data
                    </p>
                    @livewire('admin.export-data', ['type' => 'wali_kelas'])
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-3">
                <div class="relative flex-1 min-w-[160px]">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama wali kelas atau NUPTK..."
                        class="w-full h-9 pl-8 pr-8 text-xs rounded-lg border border-gray-200
                               bg-gray-50 focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    @if ($search)
                        <button wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-xmark text-xs"></i>
                        </button>
                    @endif
                </div>
                <select wire:model.live="sortBy"
                    class="h-9 px-2 text-xs rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition">
                    <option value="terbaru">Terbaru</option>
                    <option value="az">A → Z</option>
                    <option value="za">Z → A</option>
                </select>
                <select wire:model.live="perPage"
                    class="h-9 px-2 text-xs rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:border-[#F5B800] outline-none transition w-16">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        @else
            @if ($trashCount > 0)
                <div class="flex justify-end mb-3">
                    <button wire:click="emptyTrash"
                        wire:confirm="Hapus SEMUA data di tong sampah secara permanen?"
                        class="flex items-center gap-1 text-xs font-semibold px-3 py-1.5
                               bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fas fa-trash"></i> Kosongkan Semua
                    </button>
                </div>
            @endif
        @endif

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                    <tr>
                        <th class="px-3 py-2 text-left font-bold">No</th>
                        <th class="px-3 py-2 text-left font-bold">Nama</th>
                        <th class="px-3 py-2 text-left font-bold">NUPTK</th>
                        <th class="px-3 py-2 text-left font-bold">Jabatan</th>
                        <th class="px-3 py-2 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($dataWK as $w)
                        @php $isEditingRow = ($isEdit ?? false) && ($id_walikelas ?? null) == $w->id_walikelas; @endphp
                        <tr wire:key="wk-{{ $w->id_walikelas }}"
                            class="hover:bg-gray-50 transition {{ $isEditingRow ? 'bg-[rgba(245,184,0,0.07)]' : '' }}"
                            style="{{ $isEditingRow ? 'outline:1.5px solid rgba(245,184,0,0.35);outline-offset:-1px;' : '' }}">

                            <td class="px-3 py-2 text-gray-400 text-xs">{{ $dataWK->firstItem() + $loop->index }}</td>

                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <div style="width:24px;height:24px;border-radius:50%;flex-shrink:0;
                                                background:linear-gradient(135deg,#0D2D6B,#163580);
                                                color:#F5B800;font-size:10px;font-weight:700;
                                                display:flex;align-items:center;justify-content:center;">
                                        {{ strtoupper(substr($w->pengguna->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-[#0D2D6B] text-xs block">
                                            {{ $w->pengguna->name ?? '-' }}
                                            @if ($w->trashed())
                                                <span style="font-size:10px;color:#DC2626;background:rgba(229,62,62,0.08);
                                                             padding:1px 6px;border-radius:20px;margin-left:4px;font-weight:600;">Dihapus</span>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-gray-400">{{ $w->pengguna->username ?? '' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-3 py-2 text-gray-500 text-xs">{{ $w->nuptk ?? '-' }}</td>

                            <td class="px-3 py-2">
                                @if ($w->jabatan)
                                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-50 text-blue-600">{{ $w->jabatan }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @if ($showTrash)
                                    <button wire:click="restore({{ $w->id_walikelas }})"
                                        class="text-xs font-semibold mr-2 transition" style="color:#276749;">
                                        <i class="fas fa-rotate-left"></i> Pulihkan
                                    </button>
                                    <button wire:click="forceDelete({{ $w->id_walikelas }})"
                                        wire:confirm="Hapus permanen? Data tidak bisa dikembalikan."
                                        class="text-red-500 text-xs font-semibold hover:text-red-700 transition">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                @else
                                    <button wire:click="edit({{ $w->id_walikelas }})"
                                        @click="$dispatch('open-form')"
                                        class="text-[#0D2D6B] text-xs font-semibold hover:text-[#163580] mr-2 transition">
                                        {{ $isEditingRow ? '✎ Diedit' : 'Edit' }}
                                    </button>
                                    @if (!$isEditingRow)
                                        <button wire:click="hapus({{ $w->id_walikelas }})"
                                            wire:confirm="Pindahkan data ini ke tong sampah?"
                                            class="text-red-500 text-xs font-semibold hover:text-red-700 transition">
                                            Hapus
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400 text-xs">
                                <i class="fas fa-{{ $showTrash ? 'trash' : 'chalkboard-user' }} block text-2xl mb-2 opacity-25"></i>
                                {{ $showTrash ? 'Tong sampah kosong.' : 'Tidak ada data wali kelas ditemukan.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dataWK->hasPages())
            <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                <span class="text-xs text-[#4A5E8A]">
                    Menampilkan {{ $dataWK->firstItem() }}–{{ $dataWK->lastItem() }} dari {{ $dataWK->total() }} data
                </span>
                <div class="flex items-center gap-1">
                    @if ($dataWK->onFirstPage())
                        <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none"><i class="fas fa-chevron-left text-xs"></i></span>
                    @else
                        <button wire:click="previousPage" class="simdis-page-btn"><i class="fas fa-chevron-left text-xs"></i></button>
                    @endif
                    @foreach ($dataWK->getUrlRange(max(1, $dataWK->currentPage() - 2), min($dataWK->lastPage(), $dataWK->currentPage() + 2)) as $page => $url)
                        @if ($page == $dataWK->currentPage())
                            <span class="simdis-page-btn simdis-page-active">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="simdis-page-btn">{{ $page }}</button>
                        @endif
                    @endforeach
                    @if ($dataWK->hasMorePages())
                        <button wire:click="nextPage" class="simdis-page-btn"><i class="fas fa-chevron-right text-xs"></i></button>
                    @else
                        <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none"><i class="fas fa-chevron-right text-xs"></i></span>
                    @endif
                </div>
            </div>
        @else
            <div class="mt-2 text-[11px] text-gray-400">Total {{ $dataWK->total() }} data</div>
        @endif

    </div>

</div>