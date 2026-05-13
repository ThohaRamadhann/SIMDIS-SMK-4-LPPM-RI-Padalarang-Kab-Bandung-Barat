<div class="max-w-7xl mx-auto p-4 space-y-4">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h1 class="text-[22px] font-bold text-[#0D2D6B]">Data Pelanggaran</h1>
            <p class="text-sm text-gray-500">Manajemen pelanggaran siswa SIMDIS</p>
        </div>

        @if($role !== 'orang_tua')
            <a href="{{ route('pelanggaran.create') }}"
               class="inline-flex items-center justify-center bg-[#0D2D6B] text-white
                      px-4 py-2 rounded-xl text-sm hover:bg-[#163580]">
                + Tambah Pelanggaran
            </a>
        @endif
    </div>

    {{-- FLASH --}}
    @if(session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- RINGKASAN STATUS (khusus guru_bk & wali_kelas) --}}
    @if($role === 'guru_bk' || $role === 'wali_kelas')
        <div class="grid grid-cols-3 gap-3">

            <button wire:click="$set('filterStatus', '')"
                    class="flex items-center gap-3 bg-white border rounded-xl px-4 py-3 text-left
                           hover:shadow-md transition-shadow
                           {{ $filterStatus === '' ? 'border-[#0D2D6B] ring-2 ring-[#0D2D6B]/10' : 'border-gray-100' }}">
                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-base flex-shrink-0">
                    📋
                </div>
                <div>
                    <div class="text-[11px] font-600 text-gray-500 uppercase tracking-wide">Semua</div>
                    <div class="text-xl font-bold text-[#0D2D6B]">{{ $pelanggarans->total() }}</div>
                </div>
            </button>

            <button wire:click="$set('filterStatus', 'Belum Ditindak')"
                    class="flex items-center gap-3 bg-white border rounded-xl px-4 py-3 text-left
                           hover:shadow-md transition-shadow
                           {{ $filterStatus === 'Belum Ditindak' ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-100' }}">
                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center text-base flex-shrink-0">
                    ⏳
                </div>
                <div>
                    <div class="text-[11px] font-600 text-gray-500 uppercase tracking-wide">Belum Ditindak</div>
                    <div class="text-xl font-bold text-red-600">
                        {{ $pelanggarans->getCollection()->where('status_pembinaan', 'Belum Ditindak')->count() }}
                    </div>
                </div>
            </button>

            <button wire:click="$set('filterStatus', 'Dalam Proses')"
                    class="flex items-center gap-3 bg-white border rounded-xl px-4 py-3 text-left
                           hover:shadow-md transition-shadow
                           {{ $filterStatus === 'Dalam Proses' ? 'border-yellow-400 ring-2 ring-yellow-100' : 'border-gray-100' }}">
                <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center text-base flex-shrink-0">
                    🔄
                </div>
                <div>
                    <div class="text-[11px] font-600 text-gray-500 uppercase tracking-wide">Dalam Proses</div>
                    <div class="text-xl font-bold text-yellow-600">
                        {{ $pelanggarans->getCollection()->where('status_pembinaan', 'Dalam Proses')->count() }}
                    </div>
                </div>
            </button>

        </div>
    @endif

    {{-- FILTER --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">

            {{-- SEARCH --}}
            <div class="xl:col-span-2">
                <input type="text" wire:model.live="search"
                       placeholder="Cari nama siswa / NIS..."
                       class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm
                              focus:border-[#F5B800] focus:ring-[#F5B800]">
            </div>

            {{-- FILTER JENIS --}}
            <div>
                <select wire:model.live="filterJenis"
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisList as $j)
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
                <select wire:model.live="sortBy"
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="terbaru">Terbaru</option>
                    <option value="terlama">Terlama</option>
                    <option value="az">Siswa A-Z</option>
                    <option value="za">Siswa Z-A</option>
                </select>
            </div>

        </div>

        {{-- SECOND FILTER --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">

            @if($role === 'admin' || $role === 'guru_bk')
                <div>
                    <select wire:model.live="filterWaliKelas"
                            class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                        <option value="">Semua Wali Kelas</option>
                        @foreach($waliKelasList as $wk)
                            <option value="{{ $wk->id_walikelas }}">{{ $wk->pengguna->name ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <select wire:model.live="perPage"
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm">
                    <option value="10">10 Data</option>
                    <option value="25">25 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                </select>
            </div>

            @if($role === 'admin' || $role === 'guru_bk')
                <div class="flex items-center gap-2">
                    <button wire:click="$toggle('showTrash')"
                            class="px-4 h-11 rounded-xl text-sm
                                   {{ $showTrash ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Trash ({{ $trashCount }})
                    </button>
                    @if($showTrash && $trashCount > 0)
                        <button wire:click="emptyTrash"
                                onclick="confirm('Kosongkan sampah?') || event.stopImmediatePropagation()"
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
                        <th class="px-3 py-3">Wali Kelas</th>
                        <th class="px-3 py-3">Jenis Pelanggaran</th>
                        <th class="px-3 py-3">Tingkat</th>
                        <th class="px-3 py-3">Status Pembinaan</th>
                        <th class="px-3 py-3">Waktu Kejadian</th>
                        <th class="px-3 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggarans as $p)
                        <tr class="hover:bg-gray-50">

                            {{-- NO --}}
                            <td class="px-3 py-3 text-center text-gray-500">
                                {{ $pelanggarans->firstItem() + $loop->index }}
                            </td>

                            {{-- SISWA --}}
                            <td class="px-3 py-3">
                                <div class="font-semibold text-[#0D2D6B]">{{ $p->siswa->nama ?? '-' }}</div>
                            </td>

                            {{-- NIS --}}
                            <td class="px-3 py-3 text-gray-600">{{ $p->siswa->nis ?? '-' }}</td>

                            {{-- KELAS --}}
                            <td class="px-3 py-3 text-gray-600">
                                @if($p->siswa?->kelas)
                                    {{ $p->siswa->kelas->tingkat }}
                                    {{ $p->siswa->kelas->nama_kelas }}
                                    @if($p->siswa->kelas->jurusan) - {{ $p->siswa->kelas->jurusan }} @endif
                                @else -
                                @endif
                            </td>

                            {{-- WALI KELAS --}}
                            <td class="px-3 py-3 text-gray-600">{{ $p->waliKelas?->pengguna?->name ?? '-' }}</td>

                            {{-- JENIS --}}
                            <td class="px-3 py-3 text-gray-700">{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>

                            {{-- TINGKAT --}}
                            <td class="px-3 py-3">
                                @php $tingkat = $p->jenisPelanggaran->tingkat_pelanggaran ?? '-'; @endphp
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold
                                    {{ $tingkat === 'ringan' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $tingkat === 'sedang' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $tingkat === 'berat'  ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($tingkat) }}
                                </span>
                            </td>

                            {{-- STATUS PEMBINAAN --}}
                            <td class="px-3 py-3">
                                @php $status = $p->status_pembinaan; @endphp

                                @if(!$showTrash && ($role === 'guru_bk' || $role === 'wali_kelas'))
                                    {{-- Klik badge untuk buka modal --}}
                                    <button wire:click="bukaModalStatus({{ $p->id_pelanggaran }})"
                                            title="Klik untuk update status"
                                            class="px-2 py-1 rounded-lg text-xs font-semibold cursor-pointer
                                                   hover:opacity-80 transition-opacity
                                                   {{ $status === 'Belum Ditindak' ? 'bg-red-100 text-red-700' : '' }}
                                                   {{ $status === 'Dalam Proses'   ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                   {{ $status === 'Selesai'        ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ $status ?? '-' }}
                                        <span class="ml-1 opacity-60">✏️</span>
                                    </button>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold
                                        {{ $status === 'Belum Ditindak' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $status === 'Dalam Proses'   ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $status === 'Selesai'        ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ $status ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            {{-- WAKTU --}}
                            <td class="px-3 py-3 whitespace-nowrap text-gray-600">
                                {{ \Carbon\Carbon::parse($p->waktu_kejadian)->format('d-m-Y H:i') }}
                            </td>

                            {{-- AKSI --}}
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if(!$showTrash)
                                    @if($role !== 'orang_tua')
                                        <a href="{{ route('pelanggaran.edit', $p->id_pelanggaran) }}"
                                           class="text-[#0D2D6B] text-xs font-semibold hover:underline mr-2">
                                            Edit
                                        </a>
                                    @endif

                                    @if($role === 'guru_bk' || $role === 'wali_kelas')
                                        <button wire:click="bukaModalStatus({{ $p->id_pelanggaran }})"
                                                class="text-emerald-600 text-xs font-semibold hover:underline mr-2">
                                            Tindak
                                        </button>
                                    @endif

                                    @if($role === 'admin' || $role === 'guru_bk')
                                        <button wire:click="hapus({{ $p->id_pelanggaran }})"
                                                onclick="confirm('Hapus data?') || event.stopImmediatePropagation()"
                                                class="text-red-500 text-xs font-semibold hover:underline">
                                            Hapus
                                        </button>
                                    @endif
                                @else
                                    <button wire:click="restore({{ $p->id_pelanggaran }})"
                                            class="text-green-600 text-xs font-semibold hover:underline mr-2">
                                        Restore
                                    </button>
                                    <button wire:click="forceDelete({{ $p->id_pelanggaran }})"
                                            onclick="confirm('Hapus permanen?') || event.stopImmediatePropagation()"
                                            class="text-red-600 text-xs font-semibold hover:underline">
                                        Hapus Permanen
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-10 text-gray-400">
                                Tidak ada data pelanggaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">{{ $pelanggarans->links() }}</div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL UPDATE STATUS PEMBINAAN
    ══════════════════════════════════════════════════════ --}}
    @if($showModalStatus)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data style="background: rgba(0,0,0,0.45)">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md"
                 @click.outside="$wire.tutupModalStatus()">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-5 py-4
                            border-b border-gray-100">
                    <div>
                        <h3 class="font-bold text-[#0D2D6B] text-base">Update Status Pembinaan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Siswa: <strong>{{ $modalSiswa }}</strong></p>
                    </div>
                    <button wire:click="tutupModalStatus"
                            class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
                </div>

                {{-- Modal Body --}}
                <div class="px-5 py-4 space-y-4">

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Status Pembinaan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">

                            <button wire:click="$set('modalStatus', 'Belum Ditindak')"
                                    type="button"
                                    class="py-2 px-3 rounded-xl text-xs font-semibold border-2 transition-all
                                           {{ $modalStatus === 'Belum Ditindak'
                                                ? 'border-red-400 bg-red-50 text-red-700'
                                                : 'border-gray-200 text-gray-500 hover:border-red-200' }}">
                                ⏳ Belum Ditindak
                            </button>

                            <button wire:click="$set('modalStatus', 'Dalam Proses')"
                                    type="button"
                                    class="py-2 px-3 rounded-xl text-xs font-semibold border-2 transition-all
                                           {{ $modalStatus === 'Dalam Proses'
                                                ? 'border-yellow-400 bg-yellow-50 text-yellow-700'
                                                : 'border-gray-200 text-gray-500 hover:border-yellow-200' }}">
                                🔄 Dalam Proses
                            </button>

                            <button wire:click="$set('modalStatus', 'Selesai')"
                                    type="button"
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

                    {{-- Tanggal Pembinaan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Tanggal Pembinaan
                        </label>
                        <input type="date"
                               wire:model="modalTanggal"
                               class="w-full h-10 px-3 rounded-xl border border-gray-200 text-sm
                                      focus:border-[#F5B800] focus:ring-[#F5B800]">
                        @error('modalTanggal')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catatan BK --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Catatan BK
                        </label>
                        <textarea wire:model="modalCatatan"
                                  rows="3"
                                  placeholder="Tuliskan catatan hasil pembinaan..."
                                  class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm
                                         focus:border-[#F5B800] focus:ring-[#F5B800] resize-none"></textarea>
                        @error('modalCatatan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-100">
                    <button wire:click="tutupModalStatus"
                            class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600
                                   hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="simpanStatus"
                            class="px-5 py-2 rounded-xl bg-[#0D2D6B] text-white text-sm font-semibold
                                   hover:bg-[#163580] transition-colors">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>