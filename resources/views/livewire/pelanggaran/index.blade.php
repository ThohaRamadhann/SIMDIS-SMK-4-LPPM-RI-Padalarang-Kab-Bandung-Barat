<div class="max-w-7xl mx-auto p-4 space-y-4">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h1 class="text-[22px] font-bold text-[#0D2D6B]">Data Pelanggaran</h1>
            <p class="text-sm text-gray-500">Manajemen pelanggaran siswa SIMDIS</p>
        </div>
        
        {{-- Tambah hanya untuk admin & guru_bk --}}
        @if ($role === 'guru_bk')
            <a href="{{ route('pelanggaran.create') }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 bg-[#0D2D6B] text-white
       px-4 py-2 rounded-xl text-sm hover:bg-[#163580] transition-all">
                <i class="fas fa-plus text-xs"></i>
                Tambah Pelanggaran
            </a>
        @endif
    </div>

    {{-- FLASH --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- RINGKASAN STATUS (hanya guru_bk & wali_kelas) --}}
    @if (in_array($role, ['guru_bk', 'wali_kelas']))
        <div class="grid grid-cols-3 gap-2 sm:gap-3">

            {{-- SEMUA --}}
            <button wire:click="$set('filterStatus', '')"
                class="flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-3
                   bg-white border rounded-xl px-2 sm:px-4 py-3 text-center sm:text-left
                   hover:shadow-md transition-shadow
                   {{ $filterStatus === '' ? 'border-[#0D2D6B] ring-2 ring-[#0D2D6B]/10' : 'border-gray-100' }}">
                <div
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-gray-100
                    flex items-center justify-center text-sm sm:text-base flex-shrink-0">
                    📋
                </div>
                <div>
                    <div
                        class="text-[9px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide leading-tight">
                        Semua
                    </div>
                    <div class="text-lg sm:text-xl font-bold text-[#0D2D6B]">
                        {{ $pelanggarans->total() }}
                    </div>
                </div>
            </button>

            {{-- BELUM DITINDAK --}}
            <button wire:click="$set('filterStatus', 'Belum Ditindak')"
                class="flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-3
                   bg-white border rounded-xl px-2 sm:px-4 py-3 text-center sm:text-left
                   hover:shadow-md transition-shadow
                   {{ $filterStatus === 'Belum Ditindak' ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-100' }}">
                <div
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100
                    flex items-center justify-center text-sm sm:text-base flex-shrink-0">
                    ⏳
                </div>
                <div>
                    <div
                        class="text-[9px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide leading-tight">
                        <span class="sm:hidden">Belum</span>
                        <span class="hidden sm:inline">Belum Ditindak</span>
                    </div>
                    <div class="text-lg sm:text-xl font-bold text-red-600">
                        {{ $pelanggarans->getCollection()->where('status_pembinaan', 'Belum Ditindak')->count() }}
                    </div>
                </div>
            </button>

            {{-- DALAM PROSES --}}
            <button wire:click="$set('filterStatus', 'Dalam Proses')"
                class="flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-3
                   bg-white border rounded-xl px-2 sm:px-4 py-3 text-center sm:text-left
                   hover:shadow-md transition-shadow
                   {{ $filterStatus === 'Dalam Proses' ? 'border-yellow-400 ring-2 ring-yellow-100' : 'border-gray-100' }}">
                <div
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-100
                    flex items-center justify-center text-sm sm:text-base flex-shrink-0">
                    🔄
                </div>
                <div>
                    <div
                        class="text-[9px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide leading-tight">
                        <span class="sm:hidden">Proses</span>
                        <span class="hidden sm:inline">Dalam Proses</span>
                    </div>
                    <div class="text-lg sm:text-xl font-bold text-yellow-600">
                        {{ $pelanggarans->getCollection()->where('status_pembinaan', 'Dalam Proses')->count() }}
                    </div>
                </div>
            </button>

        </div>
    @endif

    {{-- FILTER --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">

            {{-- SEARCH — sembunyikan untuk orang_tua --}}
            @if ($role !== 'orang_tua')
                <div class="xl:col-span-2">
                    <input type="text" wire:model.live="search" placeholder="Cari nama siswa / NIS..."
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm
                                  focus:border-[#F5B800] focus:ring-[#F5B800]">
                </div>
            @endif

            {{-- FILTER JENIS --}}
            <div>
                <select wire:model.live="filterJenis"
                    class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j->id_jenispelanggaran }}">{{ $j->nama_pelanggaran }}</option>
                    @endforeach
                </select>
            </div>

            {{-- FILTER TINGKAT --}}
            <div>
                <select wire:model.live="filterTingkat"
                    class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="">Semua Tingkat</option>
                    <option value="ringan">Ringan</option>
                    <option value="sedang">Sedang</option>
                    <option value="berat">Berat</option>
                </select>
            </div>

            {{-- FILTER STATUS --}}
            <div>
                <select wire:model.live="filterStatus"
                    class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="">Semua Status</option>
                    <option value="Belum Ditindak">Belum Ditindak</option>
                    <option value="Dalam Proses">Dalam Proses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            {{-- SORT --}}
            <div>
                <select wire:model.live="sortBy" class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="terbaru">Terbaru</option>
                    <option value="terlama">Terlama</option>
                    <option value="az">Siswa A-Z</option>
                    <option value="za">Siswa Z-A</option>
                </select>
            </div>

        </div>

        {{-- SECOND ROW FILTER --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">

            {{-- Filter wali kelas hanya admin & guru_bk --}}
            @if (in_array($role, ['admin', 'guru_bk']))
                <div>
                    <select wire:model.live="filterWaliKelas"
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                        <option value="">Semua Wali Kelas</option>
                        @foreach ($waliKelasList as $wk)
                            <option value="{{ $wk->id_walikelas }}">{{ $wk->pengguna->name ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <select wire:model.live="perPage" class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="10">10 Data</option>
                    <option value="25">25 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                </select>
            </div>

            {{-- Trash hanya admin & guru_bk --}}
            @if (in_array($role, ['admin', 'guru_bk']))
                <div class="flex items-center gap-2">
                    <button wire:click="$toggle('showTrash')"
                        class="px-4 h-11 rounded-xl text-sm
                                   {{ $showTrash ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Trash ({{ $trashCount }})
                    </button>
                    @if ($showTrash && $trashCount > 0)
                        {{-- ++ TAMBAHAN: Kosongkan pakai custom modal ++ --}}
                        <button
                            onclick="simdisConfirm({
                                    title: 'Kosongkan Semua Sampah?',
                                    message: 'Seluruh data yang ada di trash akan dihapus secara permanen dan tidak dapat dipulihkan kembali.',
                                    confirmText: 'Ya, Kosongkan',
                                    type: 'danger',
                                    onConfirm: () => @this.emptyTrash()
                                })"
                            class="px-4 h-11 rounded-xl bg-red-600 text-white text-sm">
                            Kosongkan
                        </button>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[#0D2D6B]">
                    <tr>
                        <th class="px-3 py-3 text-center">No</th>
                        <th class="px-3 py-3 text-left">Siswa</th>
                        <th class="px-3 py-3">NIS</th>
                        <th class="px-3 py-3">Kelas</th>
                        <th class="px-3 py-3">Jurusan</th>
                        <th class="px-3 py-3">Tahun Ajaran</th>
                        {{-- Kolom Wali Kelas hanya untuk admin & guru_bk --}}
                        @if (in_array($role, ['admin', 'guru_bk']))
                            <th class="px-3 py-3">Wali Kelas</th>
                        @endif
                        <th class="px-3 py-3">Jenis Pelanggaran</th>
                        <th class="px-3 py-3">Tingkat</th>
                        <th class="px-3 py-3">Status Pembinaan</th>
                        <th class="px-3 py-3">Waktu Kejadian</th>
                        {{-- Kolom Aksi hanya untuk admin & guru_bk --}}
                        @if (in_array($role, ['admin', 'guru_bk']))
                            <th class="px-3 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggarans as $p)
                        <tr class="hover:bg-gray-50">

                            <td class="px-3 py-3 text-center text-gray-500">
                                {{ $pelanggarans->firstItem() + $loop->index }}
                            </td>

                            <td class="px-3 py-3">
                                <div class="font-semibold text-[#0D2D6B]">{{ $p->siswa->nama ?? '-' }}</div>
                            </td>

                            <td class="px-3 py-3 text-gray-600">{{ $p->siswa->nis ?? '-' }}</td>

                            <td class="px-3 py-3 text-gray-600">
                                {{ $p->siswa?->kelas?->nama_kelas ?? '-' }}
                            </td>

                            <td class="px-3 py-3 text-gray-600">
                                {{ $p->siswa?->kelas?->jurusan ?? '-' }}
                            </td>

                            <td class="px-3 py-3 text-gray-600">
                                {{ $p->siswa?->kelas?->tahun_ajaran ?? '-' }}
                            </td>

                            {{-- Wali Kelas hanya admin & guru_bk --}}
                            @if (in_array($role, ['admin', 'guru_bk']))
                                <td class="px-3 py-3 text-gray-600">
                                    {{ $p->waliKelas?->pengguna?->name ?? '-' }}
                                </td>
                            @endif

                            <td class="px-3 py-3 text-gray-700">
                                {{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}
                            </td>

                            <td class="px-3 py-3">
                                @php $tingkat = $p->jenisPelanggaran->tingkat_pelanggaran ?? '-'; @endphp
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-semibold
                                    {{ $tingkat === 'ringan' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $tingkat === 'sedang' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $tingkat === 'berat' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($tingkat) }}
                                </span>
                            </td>

                            <td class="px-3 py-3">
                                @php $status = $p->status_pembinaan; @endphp
                                {{-- Badge klik hanya untuk guru_bk --}}
                                @if (!$showTrash && $role === 'guru_bk')
                                    <button wire:click="bukaModalStatus({{ $p->id_pelanggaran }})"
                                        title="Klik untuk update status"
                                        class="px-2 py-1 rounded-lg text-xs font-semibold cursor-pointer
                                                   hover:opacity-80 transition-opacity
                                                   {{ $status === 'Belum Ditindak' ? 'bg-red-100 text-red-700' : '' }}
                                                   {{ $status === 'Dalam Proses' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                   {{ $status === 'Selesai' ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ $status ?? '-' }} <span class="ml-1 opacity-60">✏️</span>
                                    </button>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs font-semibold
                                        {{ $status === 'Belum Ditindak' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $status === 'Dalam Proses' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $status === 'Selesai' ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ $status ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-3 py-3 whitespace-nowrap text-gray-600">
                                {{ \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i') }}
                            </td>

                            {{-- Aksi hanya admin & guru_bk --}}
                            @if (in_array($role, ['admin', 'guru_bk']))
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    @if (!$showTrash)
                                        {{-- ++ TAMBAHAN: onclick showPageLoading() pada link Edit ++ --}}
                                        <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}"
                                            onclick="showPageLoading()"
                                            class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                            Edit
                                        </a>
                                        @if ($role === 'guru_bk')
                                            <button wire:click="bukaModalStatus({{ $p->id_pelanggaran }})"
                                                class="text-emerald-600 text-xs font-semibold hover:underline mr-2">
                                                Tindak
                                            </button>

                                            @if (($p->jenisPelanggaran->tingkat_pelanggaran ?? '') === 'Berat')
                                                <a href="{{ route('surat-panggilan.create', $p->id_pelanggaran) }}"
                                                    wire:navigate
                                                    class="text-purple-600 text-xs font-semibold hover:underline mr-2"
                                                    title="Buat Surat Panggilan Orang Tua">
                                                    📄 Surat
                                                </a>
                                            @endif
                                        @endif

                                        {{-- ++ TAMBAHAN: Hapus pakai custom modal, ganti wire:confirm ++ --}}
                                        <button
                                            onclick="simdisConfirm({
                                                title: 'Hapus Data Pelanggaran?',
                                                message: 'Data pelanggaran atas nama <strong>{{ addslashes($p->siswa->nama ?? '-') }}</strong> akan dipindahkan ke trash. Anda masih bisa memulihkannya.',
                                                confirmText: 'Ya, Hapus',
                                                type: 'warning',
                                                onConfirm: () => @this.hapus({{ $p->id_pelanggaran }})
                                            })"
                                            class="text-red-500 text-xs font-semibold hover:underline">
                                            Hapus
                                        </button>
                                    @else
                                        <button wire:click="restore({{ $p->id_pelanggaran }})"
                                            class="text-green-600 text-xs font-semibold hover:underline mr-2">
                                            Restore
                                        </button>

                                        {{-- ++ TAMBAHAN: Hapus Permanen pakai custom modal ++ --}}
                                        <button
                                            onclick="simdisConfirm({
                                                title: 'Hapus Permanen?',
                                                message: 'Data pelanggaran atas nama <strong>{{ addslashes($p->siswa->nama ?? '-') }}</strong> akan dihapus selamanya dan <u>tidak dapat dipulihkan</u>.',
                                                confirmText: 'Hapus Permanen',
                                                type: 'danger',
                                                onConfirm: () => @this.forceDelete({{ $p->id_pelanggaran }})
                                            })"
                                            class="text-red-600 text-xs font-semibold hover:underline">
                                            Hapus Permanen
                                        </button>
                                    @endif
                                </td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($role, ['admin', 'guru_bk']) ? 10 : 8 }}"
                                class="text-center py-10 text-gray-400">
                                Tidak ada data pelanggaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pelanggarans->links() }}</div>
    </div>

    {{-- MODAL UPDATE STATUS PEMBINAAN --}}
    @if ($showModalStatus)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.45)">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" x-data="{
                status: '{{ $modalStatus }}',
                syncFromLivewire() {
                    this.status = this.$wire.modalStatus;
                }
            }"
                x-init="$watch('$wire.modalStatus', val => status = val)" @click.outside="$wire.tutupModalStatus()">

                {{-- ── HEADER ─────────────────────────────────────────────────── --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-bold text-[#0D2D6B] text-base">Update Status Pembinaan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Siswa: <strong>{{ $modalSiswa }}</strong>
                        </p>
                    </div>
                    <button wire:click="tutupModalStatus"
                        class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
                </div>

                {{-- ── BODY ────────────────────────────────────────────────────── --}}
                <div class="px-5 py-4 space-y-4">

                    {{-- Status Pembinaan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Status Pembinaan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">

                            <button type="button" wire:click="$set('modalStatus', 'Belum Ditindak')"
                                @click="status = 'Belum Ditindak'"
                                class="py-2 px-3 rounded-xl text-xs font-semibold border-2 transition-all
                                   {{ $modalStatus === 'Belum Ditindak'
                                       ? 'border-red-400 bg-red-50 text-red-700'
                                       : 'border-gray-200 text-gray-500 hover:border-red-200' }}">
                                ⏳ Belum Ditindak
                            </button>

                            <button type="button" wire:click="$set('modalStatus', 'Dalam Proses')"
                                @click="status = 'Dalam Proses'"
                                class="py-2 px-3 rounded-xl text-xs font-semibold border-2 transition-all
                                   {{ $modalStatus === 'Dalam Proses'
                                       ? 'border-yellow-400 bg-yellow-50 text-yellow-700'
                                       : 'border-gray-200 text-gray-500 hover:border-yellow-200' }}">
                                🔄 Dalam Proses
                            </button>

                            <button type="button" wire:click="$set('modalStatus', 'Selesai')"
                                @click="status = 'Selesai'"
                                class="py-2 px-3 rounded-xl text-xs font-semibold border-2 transition-all
                                   {{ $modalStatus === 'Selesai'
                                       ? 'border-green-400 bg-green-50 text-green-700'
                                       : 'border-gray-200 text-gray-500 hover:border-green-200' }}">
                                ✅ Selesai
                            </button>

                        </div>
                        @error('modalStatus')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info hint berdasarkan status yang dipilih --}}
                    <div x-show="status !== ''" x-transition>
                        <template x-if="status === 'Belum Ditindak'">
                            <p class="text-xs text-gray-400 bg-gray-50 rounded-lg px-3 py-2">
                                💡 Status ini tidak memerlukan tanggal, jam, maupun catatan.
                            </p>
                        </template>
                        <template x-if="status === 'Dalam Proses'">
                            <p class="text-xs text-yellow-700 bg-yellow-50 rounded-lg px-3 py-2">
                                💡 Pembinaan sedang berlangsung. Tanggal mulai wajib diisi.
                            </p>
                        </template>
                        <template x-if="status === 'Selesai'">
                            <p class="text-xs text-green-700 bg-green-50 rounded-lg px-3 py-2">
                                💡 Pembinaan selesai. Tanggal, jam, dan catatan wajib diisi sebagai dokumentasi resmi.
                            </p>
                        </template>
                    </div>

                    {{-- Tanggal Pembinaan — tampil jika Dalam Proses atau Selesai --}}
                    <div x-show="status === 'Dalam Proses' || status === 'Selesai'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Tanggal Pembinaan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="modalTanggal"
                            class="w-full h-10 px-3 rounded-xl border text-sm transition-colors
                              @error('modalTanggal') border-red-400 bg-red-50 @else border-gray-200 @enderror
                              focus:outline-none focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800]">
                        @error('modalTanggal')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jam Pembinaan — tampil hanya jika Selesai --}}
                    <div x-show="status === 'Selesai'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Jam Pembinaan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">

                            <select wire:model="modalJamHour"
                                class="flex-1 h-10 px-3 rounded-xl border text-sm transition-colors
                                   @error('modalJamHour') border-red-400 bg-red-50 @else border-gray-200 @enderror
                                   focus:outline-none focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800]">
                                <option value="">Jam</option>
                                @for ($h = 6; $h <= 18; $h++)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">
                                        {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>

                            <span class="text-gray-400 font-bold text-lg">:</span>

                            <select wire:model="modalJamMinute"
                                class="flex-1 h-10 px-3 rounded-xl border text-sm transition-colors
                                   @error('modalJamMinute') border-red-400 bg-red-50 @else border-gray-200 @enderror
                                   focus:outline-none focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800]">
                                <option value="">Menit</option>
                                @foreach (['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55'] as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>

                        </div>
                        {{-- Tampilkan salah satu error jam --}}
                        @error('modalJamHour')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        @error('modalJamMinute')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catatan BK — tampil hanya jika Selesai --}}
                    <div x-show="status === 'Selesai'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Catatan BK <span class="text-red-500">*</span>
                            <span class="font-normal text-gray-400">(min. 10 karakter)</span>
                        </label>
                        <textarea wire:model="modalCatatan" rows="3" placeholder="Tuliskan catatan hasil pembinaan..."
                            class="w-full px-3 py-2 rounded-xl border text-sm resize-none transition-colors
                                 @error('modalCatatan') border-red-400 bg-red-50 @else border-gray-200 @enderror
                                 focus:outline-none focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800]"></textarea>
                        @error('modalCatatan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── FOOTER ──────────────────────────────────────────────────── --}}
                <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-100">
                    <button wire:click="tutupModalStatus"
                        class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600
                           hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="simpanStatus" wire:loading.attr="disabled" wire:target="simpanStatus"
                        class="px-5 py-2 rounded-xl bg-[#0D2D6B] text-white text-sm font-semibold
                           hover:bg-[#163580] transition-colors disabled:opacity-60 disabled:cursor-not-allowed
                           flex items-center gap-2">
                        <span wire:loading.remove wire:target="simpanStatus">Simpan</span>
                        <span wire:loading wire:target="simpanStatus" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ++ TAMBAHAN: Loading overlay saat klik tombol Edit ++ --}}
    <div id="pageLoadingOverlay"
        style="display:none; position:fixed; inset:0; z-index:9999;
               background:rgba(13,45,107,0.18); backdrop-filter:blur(2px);
               align-items:center; justify-content:center;">
        <div
            style="background:#fff; border-radius:16px; padding:28px 36px;
                    box-shadow:0 8px 32px rgba(13,45,107,0.18);
                    display:flex; align-items:center; gap:14px;">
            <span
                style="
                display:inline-block; width:22px; height:22px;
                border:3px solid #e2e8f0;
                border-top-color:#0D2D6B;
                border-radius:50%;
                animation:simdis-spin .7s linear infinite;">
            </span>
            <span style="font-size:14px; font-weight:600; color:#0D2D6B;">
                Memuat halaman edit...
            </span>
        </div>
    </div>

    {{-- ++ TAMBAHAN: Custom modal konfirmasi hapus ++ --}}
    <div id="simdisConfirmOverlay"
        style="display:none; position:fixed; inset:0; z-index:10000;
               background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);
               align-items:center; justify-content:center; padding:16px;">

        <div id="simdisConfirmBox"
            style="background:#fff; border-radius:20px; width:100%; max-width:420px;
                   box-shadow:0 20px 60px rgba(0,0,0,0.18);
                   transform:scale(0.92); opacity:0;
                   transition:transform .2s cubic-bezier(.34,1.56,.64,1), opacity .18s ease;">

            {{-- Icon area --}}
            <div id="simdisConfirmIconWrap" style="display:flex; justify-content:center; padding:28px 24px 0;">
                <div id="simdisConfirmIcon"
                    style="width:60px; height:60px; border-radius:50%;
                           display:flex; align-items:center; justify-content:center; font-size:26px;">
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:16px 28px 24px; text-align:center;">
                <h3 id="simdisConfirmTitle"
                    style="font-size:17px; font-weight:700; color:#111827; margin:12px 0 8px;"></h3>
                <p id="simdisConfirmMessage" style="font-size:13px; color:#6b7280; line-height:1.6; margin:0;"></p>
            </div>

            {{-- Footer --}}
            <div style="display:flex; gap:10px; padding:0 20px 20px;">
                <button id="simdisConfirmCancel" onclick="simdisCloseConfirm()"
                    style="flex:1; height:42px; border-radius:12px;
                           border:1.5px solid #e5e7eb; background:#fff;
                           font-size:13px; font-weight:600; color:#6b7280;
                           cursor:pointer; transition:background .15s;">
                    Batal
                </button>
                <button id="simdisConfirmOk"
                    style="flex:1; height:42px; border-radius:12px; border:none;
                           font-size:13px; font-weight:700; color:#fff;
                           cursor:pointer; transition:opacity .15s;">
                    Konfirmasi
                </button>
            </div>

        </div>
    </div>

    <style>
        @keyframes simdis-spin {
            to {
                transform: rotate(360deg);
            }
        }

        #simdisConfirmCancel:hover {
            background: #f9fafb !important;
        }

        #simdisConfirmOk:hover {
            opacity: 0.88 !important;
        }
    </style>

    <script>
        // ── Loading overlay saat klik Edit ─────────────────────────────
        function showPageLoading() {
            var overlay = document.getElementById('pageLoadingOverlay');
            overlay.style.display = 'flex';
        }

        // ── Custom confirm modal ────────────────────────────────────────
        var _simdisConfirmCallback = null;

        var _simdisTheme = {
            warning: {
                icon: '🗑️',
                bg: '#FEF3C7',
                okColor: '#D97706',
                okBg: '#F59E0B',
            },
            danger: {
                icon: '⚠️',
                bg: '#FEE2E2',
                okColor: '#fff',
                okBg: '#DC2626',
            }
        };

        function simdisConfirm(opts) {
            var theme = _simdisTheme[opts.type] || _simdisTheme['warning'];
            var overlay = document.getElementById('simdisConfirmOverlay');
            var box = document.getElementById('simdisConfirmBox');

            document.getElementById('simdisConfirmIcon').textContent = theme.icon;
            document.getElementById('simdisConfirmIcon').style.background = theme.bg;
            document.getElementById('simdisConfirmTitle').textContent = opts.title || 'Konfirmasi';
            document.getElementById('simdisConfirmMessage').innerHTML = opts.message || '';
            document.getElementById('simdisConfirmOk').textContent = opts.confirmText || 'Ya, Lanjutkan';
            document.getElementById('simdisConfirmOk').style.background = theme.okBg;

            _simdisConfirmCallback = opts.onConfirm || null;

            overlay.style.display = 'flex';
            requestAnimationFrame(function() {
                box.style.transform = 'scale(1)';
                box.style.opacity = '1';
            });
        }

        function simdisCloseConfirm() {
            var overlay = document.getElementById('simdisConfirmOverlay');
            var box = document.getElementById('simdisConfirmBox');
            box.style.transform = 'scale(0.92)';
            box.style.opacity = '0';
            setTimeout(function() {
                overlay.style.display = 'none';
            }, 200);
            _simdisConfirmCallback = null;
        }

        document.getElementById('simdisConfirmOk').addEventListener('click', function() {
            if (typeof _simdisConfirmCallback === 'function') {
                _simdisConfirmCallback();
            }
            simdisCloseConfirm();
        });

        // Klik di luar box = tutup
        document.getElementById('simdisConfirmOverlay').addEventListener('click', function(e) {
            if (e.target === this) simdisCloseConfirm();
        });

        // Esc = tutup
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') simdisCloseConfirm();
        });
    </script>

</div>
