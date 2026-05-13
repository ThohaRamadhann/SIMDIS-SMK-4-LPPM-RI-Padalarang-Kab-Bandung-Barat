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
                        {{ $isEdit ? 'border-[rgba(245,184,0,0.4)] ring-2 ring-[rgba(245,184,0,0.12)]' : '' }}"
                 style="transition: box-shadow .25s, border-color .25s;">

                {{-- Banner edit --}}
                @if ($isEdit)
                    <div class="-mx-4 -mt-4 mb-3 px-4 py-2 rounded-t-2xl text-[11px] font-semibold"
                         style="background:rgba(245,184,0,0.1);border-bottom:1px solid rgba(245,184,0,0.3);color:#7a5c00;">
                        <i class="fas fa-pen-to-square mr-1"></i> Mode Edit Wali Kelas
                    </div>
                @endif

                <h2 class="text-[16px] font-bold text-[#0D2D6B]">
                    {{ $isEdit ? 'Edit Data Wali Kelas' : 'Tambah Data Wali Kelas' }}
                </h2>
                <p class="text-xs text-gray-500 mb-3">Kelola data wali kelas SIMDIS</p>

                <div class="space-y-2">

                    {{-- Nama Wali Kelas --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Nama Wali Kelas *</label>
                        <select wire:model="id_pengguna"
                            wire:change="$dispatch('updatedIdPengguna', $event.target.value)"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20
                                   outline-none transition">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach ($pengguna as $p)
                                <option value="{{ $p->id_pengguna }}">
                                    {{ $p->name }} ({{ $p->username }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pengguna')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                        <p class="text-[11px] text-gray-400 mt-1">Pilih wali kelas dari pengguna terdaftar</p>
                    </div>

                    {{-- NUPTK --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">NUPTK (Opsional)</label>
                        <input type="text"
                            wire:model.defer="nuptk"
                            placeholder="Masukkan NUPTK jika ada"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20
                                   outline-none transition">
                        @error('nuptk')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label class="text-xs font-semibold text-[#0D2D6B]">Jabatan (Opsional)</label>
                        <input type="text"
                            wire:model.defer="jabatan"
                            placeholder="Contoh: Wali Kelas X IPA"
                            class="mt-0.5 w-full h-10 px-3 text-sm rounded-lg border border-gray-200 bg-gray-50
                                   focus:bg-white focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20
                                   outline-none transition">
                        @error('jabatan')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Info mode --}}
                    @if ($id_pengguna)
                        <div class="text-xs px-3 py-2 rounded-lg border
                                    {{ $isEdit
                                        ? 'bg-amber-50 text-amber-700 border-amber-100'
                                        : 'bg-blue-50 text-blue-700 border-blue-100' }}">
                            <i class="fas fa-{{ $isEdit ? 'pen-to-square' : 'plus-circle' }} mr-1"></i>
                            {{ $isEdit
                                ? 'Mode Edit: mengubah data yang sudah ada'
                                : 'Mode Tambah: akan membuat data baru' }}
                        </div>
                    @endif

                    {{-- Buttons --}}
                    <div class="flex gap-2 pt-2">
                        @if ($isEdit)
                            <button wire:click="update"
                                class="bg-[#0D2D6B] text-white px-4 py-2 text-sm font-semibold rounded-lg
                                       hover:bg-[#163580] transition">
                                <i class="fas fa-save mr-1"></i> Update
                            </button>
                            <button wire:click="resetForm"
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
                        @endif
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
                            {{ $showTrash ? 'Tong Sampah' : 'Daftar Wali Kelas' }}
                        </h3>
                        <p class="text-xs text-gray-500">
                            {{ $showTrash ? 'Data wali kelas yang telah dihapus.' : 'Data seluruh wali kelas SIMDIS' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-[#0D2D6B] bg-blue-50 px-3 py-1 rounded-full">
                            {{ $dataWK->total() }} data
                        </span>
                        {{-- Toggle trash --}}
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
                    <div class="flex flex-wrap gap-2 mb-3">

                        {{-- Search --}}
                        <div class="relative flex-1 min-w-[160px]">
                            <input type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Cari nama wali kelas atau NUPTK..."
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
                                <th class="px-3 py-2 text-left font-bold">Nama</th>
                                <th class="px-3 py-2 text-left font-bold">NUPTK</th>
                                <th class="px-3 py-2 text-left font-bold">Jabatan</th>
                                <th class="px-3 py-2 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($dataWK as $w)
                                @php $isEditingRow = $isEdit && $id_walikelas == $w->id_walikelas; @endphp
                                <tr wire:key="wk-{{ $w->id_walikelas }}"
                                    class="hover:bg-gray-50 transition
                                           {{ $isEditingRow ? 'bg-[rgba(245,184,0,0.07)]' : '' }}"
                                    style="{{ $isEditingRow ? 'outline:1.5px solid rgba(245,184,0,0.35);outline-offset:-1px;' : '' }}">

                                    {{-- No --}}
                                    <td class="px-3 py-2 text-gray-400 text-xs">
                                        {{ $dataWK->firstItem() + $loop->index }}
                                    </td>

                                    {{-- Nama --}}
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
                                                                     padding:1px 6px;border-radius:20px;margin-left:4px;font-weight:600;">
                                                            Dihapus
                                                        </span>
                                                    @endif
                                                </span>
                                                <span class="text-[10px] text-gray-400">
                                                    {{ $w->pengguna->username ?? '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- NUPTK --}}
                                    <td class="px-3 py-2 text-gray-500 text-xs">
                                        {{ $w->nuptk ?? '-' }}
                                    </td>

                                    {{-- Jabatan --}}
                                    <td class="px-3 py-2">
                                        @if ($w->jabatan)
                                            <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-50 text-blue-600">
                                                {{ $w->jabatan }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                        @if ($showTrash)
                                            <button wire:click="restore({{ $w->id_walikelas }})"
                                                class="text-xs font-semibold mr-2 transition"
                                                style="color:#276749;">
                                                <i class="fas fa-rotate-left"></i> Pulihkan
                                            </button>
                                            <button wire:click="forceDelete({{ $w->id_walikelas }})"
                                                wire:confirm="Hapus permanen? Data tidak bisa dikembalikan."
                                                class="text-red-500 text-xs font-semibold hover:text-red-700 transition">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        @else
                                            <button wire:click="edit({{ $w->id_walikelas }})"
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
                                        {{ $showTrash
                                            ? 'Tong sampah kosong.'
                                            : 'Tidak ada data wali kelas ditemukan.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ── PAGINATION ── --}}
                @if ($dataWK->hasPages())
                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs text-[#4A5E8A]">
                            Menampilkan {{ $dataWK->firstItem() }}–{{ $dataWK->lastItem() }}
                            dari {{ $dataWK->total() }} data
                        </span>
                        <div class="flex items-center gap-1">

                            @if ($dataWK->onFirstPage())
                                <span class="simdis-page-btn opacity-40 cursor-not-allowed select-none">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <button wire:click="previousPage" class="simdis-page-btn">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                            @endif

                            @foreach ($dataWK->getUrlRange(
                                max(1, $dataWK->currentPage() - 2),
                                min($dataWK->lastPage(), $dataWK->currentPage() + 2)
                            ) as $page => $url)
                                @if ($page == $dataWK->currentPage())
                                    <span class="simdis-page-btn simdis-page-active">{{ $page }}</span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" class="simdis-page-btn">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            @if ($dataWK->hasMorePages())
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
                        Total {{ $dataWK->total() }} data
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>