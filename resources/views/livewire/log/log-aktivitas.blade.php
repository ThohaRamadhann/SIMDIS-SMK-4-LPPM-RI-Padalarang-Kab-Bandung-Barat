<?php

use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public string $roleUser = '';
    public int $idPengguna = 0;

    public string $filterAksi = '';
    public string $filterModul = '';
    public string $filterTanggalDari = '';
    public string $filterTanggalSampai = '';
    public string $filterUser = '';
    public string $searchPengguna = '';
    public string $namaPenggunaTerpilih = '';

    public int $page = 1;
    public int $perPage = 15;

    public function mount(): void
    {
        $this->roleUser = optional(auth()->user()->role)->nama_role ?? '';
        $this->idPengguna = auth()->user()->id_pengguna;
    }

    public function resetFilter(): void
    {
        $this->filterAksi = '';
        $this->filterModul = '';
        $this->filterTanggalDari = '';
        $this->filterTanggalSampai = '';
        $this->filterUser = '';
        $this->searchPengguna = '';
        $this->namaPenggunaTerpilih = '';
        $this->page = 1;
    }

    public function pilihPengguna(int $id): void
    {
        $pengguna = Pengguna::find($id);
        $this->filterUser = (string) $id;
        $this->namaPenggunaTerpilih = $pengguna?->name ?? '';
        $this->searchPengguna = '';
        $this->page = 1;
    }

    public function hapusFilterPengguna(): void
    {
        $this->filterUser = '';
        $this->namaPenggunaTerpilih = '';
        $this->searchPengguna = '';
        $this->page = 1;
    }

    public function with(): array
    {
        $query = LogAktivitas::with(['pengguna.role'])->orderBy('waktu', 'desc');

        if ($this->roleUser === 'admin') {
            if ($this->filterUser) {
                $query->where('id_pengguna', $this->filterUser);
            }
        } elseif ($this->roleUser === 'guru_bk') {
            $query->where('id_pengguna', $this->idPengguna);
        } elseif ($this->roleUser === 'wali_kelas') {
            $walikelas = auth()->user()->waliKelas;
            $idSiswaDiKelas = [];
            if ($walikelas && $walikelas->kelas) {
                $idSiswaDiKelas = $walikelas->kelas->siswa->pluck('id_siswa')->toArray();
            }
            $idPelanggaranDiKelas = \App\Models\Pelanggaran::whereIn('id_siswa', $idSiswaDiKelas)->pluck('id_pelanggaran')->toArray();

            $query->where(function ($q) use ($idPelanggaranDiKelas) {
                $q->where('id_pengguna', $this->idPengguna)->orWhere(function ($q2) use ($idPelanggaranDiKelas) {
                    $q2->where('modul', 'pelanggaran')->whereIn('id_referensi', $idPelanggaranDiKelas);
                });
            });
        } elseif ($this->roleUser === 'wali_siswa') {
            $waliSiswa = auth()->user()->waliSiswa; //
            $idSiswa = $waliSiswa?->siswa->pluck('id_siswa')->toArray() ?? [];
            $idPelanggaran = \App\Models\Pelanggaran::whereIn('id_siswa', $idSiswa)->pluck('id_pelanggaran')->toArray();

            $query->where('modul', 'pelanggaran')->whereIn('id_referensi', $idPelanggaran);
        }

        if ($this->filterAksi) {
            $query->where('aksi', $this->filterAksi);
        }
        if ($this->filterModul) {
            $query->where('modul', $this->filterModul);
        }
        if ($this->filterTanggalDari) {
            $query->whereDate('waktu', '>=', $this->filterTanggalDari);
        }
        if ($this->filterTanggalSampai) {
            $query->whereDate('waktu', '<=', $this->filterTanggalSampai);
        }

        // Hanya query pengguna saat admin mengetik minimal 2 huruf, dibatasi 20 hasil
        $daftarPengguna = collect();
        if ($this->roleUser === 'admin' && strlen($this->searchPengguna) >= 2) {
            $daftarPengguna = Pengguna::with('role')
                ->where('name', 'like', '%' . $this->searchPengguna . '%')
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        $logs = $query->paginate($this->perPage);

        return [
            'logs' => $logs,
            'daftarPengguna' => $daftarPengguna,
        ];
    }
};
?>

<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-[#0D2D6B]">Log Aktivitas</h1>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            {{-- Filter Aksi --}}
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Aksi</label>
                <select wire:model.live="filterAksi"
                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0D2D6B]/20">
                    <option value="">Semua Aksi</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="tambah_pelanggaran">Tambah Pelanggaran</option>
                    <option value="edit_pelanggaran">Edit Pelanggaran</option>
                    <option value="hapus_pelanggaran">Hapus Pelanggaran</option>
                    <option value="tambah_jenis_pelanggaran">Tambah Jenis Pelanggaran</option>
                    <option value="edit_jenis_pelanggaran">Edit Jenis Pelanggaran</option>
                    <option value="hapus_jenis_pelanggaran">Hapus Jenis Pelanggaran</option>
                    @if ($roleUser === 'admin')
                        <option value="tambah_siswa">Tambah Siswa</option>
                        <option value="edit_siswa">Edit Siswa</option>
                        <option value="hapus_siswa">Hapus Siswa</option>
                        <option value="tambah_pengguna">Tambah Pengguna</option>
                        <option value="edit_pengguna">Edit Pengguna</option>
                        <option value="hapus_pengguna">Hapus Pengguna</option>
                    @endif
                </select>
            </div>

            {{-- Filter Modul --}}
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Modul</label>
                <select wire:model.live="filterModul"
                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0D2D6B]/20">
                    <option value="">Semua Modul</option>
                    <option value="auth">Auth</option>
                    <option value="pelanggaran">Pelanggaran</option>
                    <option value="jenis_pelanggaran">Jenis Pelanggaran</option>
                    @if ($roleUser === 'admin')
                        <option value="siswa">Siswa</option>
                        <option value="pengguna">Pengguna</option>
                        <option value="kelas">Kelas</option>
                    @endif
                </select>
            </div>

            {{-- Filter Tanggal Dari --}}
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Dari Tanggal</label>
                <input type="date" wire:model.live="filterTanggalDari"
                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0D2D6B]/20">
            </div>

            {{-- Filter Tanggal Sampai --}}
            <div>
                <label class="text-xs font-semibold text-gray-500 mb-1 block">Sampai Tanggal</label>
                <input type="date" wire:model.live="filterTanggalSampai"
                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0D2D6B]/20">
            </div>

            {{-- Filter User (admin only) — search-as-you-type, tidak load semua data --}}
            @if ($roleUser === 'admin')
                <div class="sm:col-span-2 relative" x-data="{ open: false }" @click.outside="open = false">
                    <label class="text-xs font-semibold text-gray-500 mb-1 block">Filter Pengguna</label>

                    @if ($namaPenggunaTerpilih)
                        <div
                            class="flex items-center justify-between w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-[#f0f4fb]">
                            <span class="font-medium text-[#0D2D6B] truncate">{{ $namaPenggunaTerpilih }}</span>
                            <button type="button" wire:click="hapusFilterPengguna"
                                class="text-gray-400 hover:text-red-500 ml-2 flex-shrink-0">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @else
                        <input type="text" wire:model.live.debounce.300ms="searchPengguna"
                            x-on:focus="open = true"
                            placeholder="Ketik nama pengguna (min. 2 huruf)..."
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0D2D6B]/20">

                        <div x-show="open" x-cloak
                            class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                            @if (strlen($searchPengguna) < 2)
                                <div class="px-3 py-2 text-xs text-gray-400">Ketik minimal 2 huruf untuk mencari
                                    pengguna</div>
                            @elseif ($daftarPengguna->isEmpty())
                                <div class="px-3 py-2 text-xs text-gray-400">Pengguna tidak ditemukan</div>
                            @else
                                @foreach ($daftarPengguna as $p)
                                    <button type="button" wire:click="pilihPengguna({{ $p->id_pengguna }})"
                                        x-on:click="open = false"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-[#f0f4fb] transition-colors">
                                        <div class="font-semibold text-[#0D2D6B]">{{ $p->name }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ str_replace('_', ' ', optional($p->role)->nama_role) }}
                                        </div>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            {{-- Reset --}}
            <div class="flex items-end">
                <button wire:click="resetFilter"
                    class="w-full text-sm font-semibold text-[#0D2D6B] border border-[#0D2D6B] rounded-lg px-3 py-2 hover:bg-[#f0f4fb] transition-colors">
                    Reset Filter
                </button>
            </div>

        </div>
    </div>

    {{-- Tabel Log --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#f0f4fb] border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">Waktu
                        </th>
                        @if ($roleUser === 'admin')
                            <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">
                                Pengguna</th>
                        @endif
                        @if (in_array($roleUser, ['wali_kelas', 'wali_siswa']))
                            <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">
                                Dicatat Oleh</th>
                        @endif
                        <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">Aksi
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">Modul
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">
                            Keterangan</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-[#0D2D6B] uppercase tracking-wide">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[#f9fafb] transition-colors">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $log->waktu->format('d/m/Y H:i:s') }}
                            </td>
                            @if ($roleUser === 'admin' || in_array($roleUser, ['wali_kelas', 'wali_siswa']))
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-[#0D2D6B]">{{ optional($log->pengguna)->name }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ str_replace('_', ' ', optional(optional($log->pengguna)->role)->nama_role) }}
                                    </div>
                                </td>
                            @endif
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ str_contains($log->aksi, 'tambah')
                                        ? 'bg-green-100 text-green-700'
                                        : (str_contains($log->aksi, 'edit')
                                            ? 'bg-blue-100 text-blue-700'
                                            : (str_contains($log->aksi, 'hapus')
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-gray-100 text-gray-700')) }}">
                                    {{ str_replace('_', ' ', $log->aksi) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ str_replace('_', ' ', $log->modul) }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->keterangan }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Tidak ada log aktivitas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>