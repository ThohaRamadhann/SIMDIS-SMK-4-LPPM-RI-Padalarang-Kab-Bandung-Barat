<div class="max-w-7xl mx-auto p-4 space-y-4">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h1 class="text-[22px] font-bold text-[#0D2D6B]">
                <i class="fas fa-chart-line mr-2" style="color:#F5B800"></i>
                Monitoring Pasca Pembinaan
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Pantau perkembangan disiplin siswa setelah mendapat pembinaan
            </p>
        </div>
    </div>

    {{-- FLASH --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── RINGKASAN CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- Total --}}
        <button wire:click="$set('filterStatus', '')"
            class="bg-white border-2 rounded-2xl p-4 text-left hover:shadow-md transition-all
                   {{ $filterStatus === '' ? 'border-[#0D2D6B] ring-2 ring-[#0D2D6B]/10' : 'border-gray-100' }}">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-[#0D2D6B]/10 flex items-center justify-center">
                    <i class="fas fa-users text-[#0D2D6B] text-sm"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Total Dipantau</span>
            </div>
            <div class="text-3xl font-bold text-[#0D2D6B]">{{ $ringkasan['total'] }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">siswa pasca pembinaan</div>
        </button>

        {{-- Baik --}}
        <button wire:click="$set('filterStatus', 'baik')"
            class="bg-white border-2 rounded-2xl p-4 text-left hover:shadow-md transition-all
                   {{ $filterStatus === 'baik' ? 'border-green-400 ring-2 ring-green-100' : 'border-gray-100' }}">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Baik</span>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $ringkasan['baik'] }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">tidak ada pelanggaran baru</div>
        </button>

        {{-- Perlu Perhatian --}}
        <button wire:click="$set('filterStatus', 'perhatian')"
            class="bg-white border-2 rounded-2xl p-4 text-left hover:shadow-md transition-all
                   {{ $filterStatus === 'perhatian' ? 'border-yellow-400 ring-2 ring-yellow-100' : 'border-gray-100' }}">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-yellow-600 text-sm"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Perlu Perhatian</span>
            </div>
            <div class="text-3xl font-bold text-yellow-600">{{ $ringkasan['perhatian'] }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">1-2 pelanggaran ringan/sedang</div>
        </button>

        {{-- Berisiko --}}
        <button wire:click="$set('filterStatus', 'berisiko')"
            class="bg-white border-2 rounded-2xl p-4 text-left hover:shadow-md transition-all
                   {{ $filterStatus === 'berisiko' ? 'border-red-400 ring-2 ring-red-100' : 'border-gray-100' }}">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                </div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Berisiko</span>
            </div>
            <div class="text-3xl font-bold text-red-600">{{ $ringkasan['berisiko'] }}</div>
            <div class="text-[10px] text-gray-400 mt-0.5">pelanggaran berat / berulang</div>
        </button>

    </div>

    {{-- ── FILTER ── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            {{-- Search --}}
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama atau NIS siswa..."
                    class="w-full h-11 pl-9 pr-4 rounded-xl border border-gray-200 text-sm
                           focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 outline-none transition">
            </div>

            {{-- Filter Kelas --}}
            <select wire:model.live="filterKelas"
                class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm
                       focus:border-[#F5B800] outline-none transition">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            {{-- Per page --}}
            <select wire:model.live="perPage"
                class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm
                       focus:border-[#F5B800] outline-none transition">
                <option value="10">10 Data</option>
                <option value="25">25 Data</option>
                <option value="50">50 Data</option>
            </select>

        </div>
    </div>

    {{-- ── TABEL ── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#0D2D6B]">
                Daftar Siswa Pasca Pembinaan
                @if($filterStatus)
                    <span class="ml-2 text-xs font-normal text-gray-400">
                        — filter: {{ ucfirst($filterStatus) }}
                    </span>
                @endif
            </h3>
            <span class="text-xs text-gray-400">{{ $total }} siswa</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[#0D2D6B] text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold">Siswa</th>
                        <th class="px-4 py-3 text-left font-bold">Kelas</th>
                        <th class="px-4 py-3 text-center font-bold">Total Pelanggaran</th>
                        <th class="px-4 py-3 text-center font-bold">Belum Ditindak</th>
                        <th class="px-4 py-3 text-left font-bold">Pembinaan Terakhir</th>
                        <th class="px-4 py-3 text-center font-bold">Status Disiplin</th>
                        <th class="px-4 py-3 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    @forelse($siswaData as $d)
                        @php
                            $siswa      = $d['siswa'];
                            $status     = $d['status'] ?? null;
                            $label      = $d['label'] ?? '-';
                            $jumlahBaru = $d['jumlah_baru'] ?? 0;
                            $tglBinaan  = $d['tgl_pembinaan'] ?? null;
                            $totalP     = $siswa->pelanggaran->count();

                            $badgeClass = match($status) {
                                'baik'      => 'bg-green-100 text-green-700',
                                'perhatian' => 'bg-yellow-100 text-yellow-700',
                                'berisiko'  => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-500',
                            };
                            $rowClass = match($status) {
                                'berisiko'  => 'bg-red-50/30',
                                'perhatian' => 'bg-yellow-50/30',
                                default     => '',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $rowClass }}">

                            {{-- Siswa --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;
                                                background:linear-gradient(135deg,#0D2D6B,#163580);
                                                color:#F5B800;font-size:11px;font-weight:700;
                                                display:flex;align-items:center;justify-content:center;">
                                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-[#0D2D6B] text-xs">{{ $siswa->nama }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $siswa->nis }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kelas --}}
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">
                                    {{ $siswa->kelas?->nama_kelas ?? '-' }}
                                </span>
                            </td>

                            {{-- Total Pelanggaran --}}
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-bold text-[#0D2D6B]">{{ $totalP }}</span>
                            </td>

                            {{-- Pelanggaran Belum Ditindak --}}
                            <td class="px-4 py-3 text-center">
                                @if($jumlahBaru > 0)
                                    <span class="text-sm font-bold text-red-600">+{{ $jumlahBaru }}</span>
                                @else
                                    <span class="text-sm font-bold text-green-600">0</span>
                                @endif
                            </td>

                            {{-- Tanggal & Jam Pembinaan Terakhir --}}
                            <td class="px-4 py-3">
                                @if($tglBinaan)
                                    @php $tgl = \Carbon\Carbon::parse($tglBinaan); @endphp
                                    <div class="text-xs text-gray-600">
                                        {{ $tgl->translatedFormat('d M Y') }}
                                        @if($tgl->format('H:i') !== '00:00')
                                            <span class="text-gray-400">pukul {{ $tgl->format('H:i') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $tgl->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            {{-- Status Disiplin --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badgeClass }}">
                                    @if($status === 'baik') ✅
                                    @elseif($status === 'perhatian') ⚠️
                                    @elseif($status === 'berisiko') 🔴
                                    @endif
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-center">
                                <button wire:click="lihatDetail({{ $siswa->id_siswa }})"
                                    class="px-3 py-1.5 bg-[#0D2D6B] text-white text-xs font-semibold
                                           rounded-lg hover:bg-[#163580] transition">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                <i class="fas fa-chart-line block text-3xl mb-2 opacity-20"></i>
                                <p class="text-sm">Belum ada siswa yang telah selesai dibina.</p>
                                <p class="text-xs mt-1 opacity-60">
                                    Siswa akan muncul di sini setelah status pembinaan diubah menjadi "Selesai"
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination manual --}}
        @if($total > $perPage)
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Menampilkan {{ $total }} siswa</span>
            </div>
        @endif

    </div>

    {{-- ── MODAL DETAIL SISWA ── --}}
    @if($showModal && $modalSiswa)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background:rgba(9,30,74,0.55);backdrop-filter:blur(3px)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col"
                 wire:click.outside="tutupModal">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
                     style="background:linear-gradient(90deg,#0D2D6B,#163580)">
                    <div class="flex items-center gap-3">
                        <div style="width:36px;height:36px;border-radius:50%;
                                    background:rgba(245,184,0,0.2);border:2px solid #F5B800;
                                    color:#F5B800;font-size:13px;font-weight:700;
                                    display:flex;align-items:center;justify-content:center;">
                            {{ strtoupper(substr($modalSiswa->nama, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-base">{{ $modalSiswa->nama }}</h3>
                            <p class="text-xs text-[#F5B800]">
                                {{ $modalSiswa->nis }} • {{ $modalSiswa->kelas?->nama_kelas ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="tutupModal"
                        class="text-white/60 hover:text-white text-xl w-8 h-8 flex items-center justify-center
                               rounded-lg hover:bg-white/10 transition">✕</button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto flex-1 px-6 py-4 space-y-4">

                    {{-- Status badge besar --}}
                    @php
                        $statusInfo = App\Livewire\Monitoring\Index::hitungStatus($modalSiswa);
                        $s = $statusInfo['status'] ?? null;
                        $badgeBig = match($s) {
                            'baik'      => 'bg-green-100 text-green-700 border-green-200',
                            'perhatian' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            'berisiko'  => 'bg-red-100 text-red-700 border-red-200',
                            default     => 'bg-gray-100 text-gray-500 border-gray-200',
                        };
                    @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl border {{ $badgeBig }}">
                        <div class="text-2xl">
                            @if($s === 'baik') ✅
                            @elseif($s === 'perhatian') ⚠️
                            @elseif($s === 'berisiko') 🔴
                            @else ❓
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-sm">Status Disiplin: {{ $statusInfo['label'] ?? '-' }}</div>
                            @if(isset($statusInfo['tgl_pembinaan']) && $statusInfo['tgl_pembinaan'])
                                @php $tglModal = \Carbon\Carbon::parse($statusInfo['tgl_pembinaan']); @endphp
                                <div class="text-xs opacity-75">
                                    Pembinaan terakhir:
                                    {{ $tglModal->translatedFormat('d M Y') }}
                                    @if($tglModal->format('H:i') !== '00:00')
                                        pukul {{ $tglModal->format('H:i') }}
                                    @endif
                                    ({{ $tglModal->diffForHumans() }})
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline riwayat pelanggaran --}}
                    <div>
                        <h4 class="text-xs font-bold text-[#0D2D6B] uppercase tracking-wide mb-3">
                            <i class="fas fa-history mr-1" style="color:#F5B800"></i>
                            Riwayat Pelanggaran
                        </h4>

                        @if($modalRiwayat->isEmpty())
                            <p class="text-xs text-gray-400 text-center py-4">Tidak ada riwayat pelanggaran.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($modalRiwayat as $p)
                                    @php
                                        $tingkat = strtolower($p->jenisPelanggaran?->tingkat_pelanggaran ?? '');

                                        // Bandingkan waktu kejadian vs waktu pembinaan (sudah include jam)
                                        $setelahBinaan = isset($statusInfo['tgl_pembinaan'])
                                            && $statusInfo['tgl_pembinaan']
                                            && \Carbon\Carbon::parse($p->waktu_kejadian)
                                                ->gt(\Carbon\Carbon::parse($statusInfo['tgl_pembinaan']));

                                        $tingkatBadge = match($tingkat) {
                                            'ringan' => 'bg-yellow-100 text-yellow-700',
                                            'sedang' => 'bg-orange-100 text-orange-700',
                                            'berat'  => 'bg-red-100 text-red-700',
                                            default  => 'bg-gray-100 text-gray-500',
                                        };
                                        $statusBadge = match($p->status_pembinaan) {
                                            'Selesai'      => 'bg-green-100 text-green-700',
                                            'Dalam Proses' => 'bg-yellow-100 text-yellow-700',
                                            default        => 'bg-red-100 text-red-600',
                                        };
                                    @endphp
                                    <div class="flex gap-3 p-3 rounded-xl border
                                                {{ $setelahBinaan ? 'border-red-200 bg-red-50/50' : 'border-gray-100 bg-gray-50' }}">

                                        <div class="flex-shrink-0 mt-0.5">
                                            @if($setelahBinaan)
                                                <div class="w-2 h-2 rounded-full bg-red-500 mt-1.5"></div>
                                            @else
                                                <div class="w-2 h-2 rounded-full bg-gray-300 mt-1.5"></div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-semibold text-[#0D2D6B]">
                                                    {{ $p->jenisPelanggaran?->nama_pelanggaran ?? '-' }}
                                                </span>
                                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $tingkatBadge }}">
                                                    {{ ucfirst($tingkat) }}
                                                </span>
                                                @if($setelahBinaan)
                                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-red-200 text-red-700">
                                                        ⚠ Setelah Pembinaan
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                                <span>
                                                    <i class="fas fa-clock mr-0.5"></i>
                                                    {{ \Carbon\Carbon::parse($p->waktu_kejadian)->translatedFormat('d M Y, H:i') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded-full {{ $statusBadge }}">
                                                    {{ $p->status_pembinaan }}
                                                </span>
                                            </div>

                                            {{-- Tanggal & jam pembinaan per item --}}
                                            @if($p->status_pembinaan === 'Selesai' && $p->tanggal_pembinaan)
                                                <div class="text-[10px] text-green-600 mt-0.5">
                                                    <i class="fas fa-check mr-0.5"></i>
                                                    Dibina:
                                                    {{ \Carbon\Carbon::parse($p->tanggal_pembinaan)->translatedFormat('d M Y') }}
                                                    @if($p->jam_pembinaan)
                                                        pukul {{ \Carbon\Carbon::parse($p->jam_pembinaan)->format('H:i') }}
                                                    @endif
                                                </div>
                                            @endif

                                            @if($p->catatan_bk)
                                                <div class="text-[10px] text-gray-500 mt-1 italic">
                                                    <i class="fas fa-quote-left mr-1 opacity-50"></i>
                                                    {{ $p->catatan_bk }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Legenda --}}
                            <div class="flex items-center gap-4 mt-3 text-[10px] text-gray-400">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                    Sebelum pembinaan
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    Setelah pembinaan
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('pelanggaran.index') }}?search={{ $modalSiswa->nama }}"
                        class="text-xs text-[#0D2D6B] font-semibold hover:underline">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Lihat di Data Pelanggaran
                    </a>
                    <button wire:click="tutupModal"
                        class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>