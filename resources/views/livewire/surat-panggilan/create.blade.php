<?php

use App\Models\Pelanggaran;
use App\Models\SuratPanggilan;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    public int $idPelanggaran;

    // Data pelanggaran (read only, untuk ditampilkan)
    public string $namaSiswa      = '';
    public string $namaKelas      = '';
    public string $jenisPelanggaran = '';
    public string $tingkat        = '';
    public string $waktuKejadian  = '';

    // Form fields
    public string $tanggalPanggilan = '';
    public string $waktuJam         = '08';
    public string $waktuMenit       = '00';
    public string $tempat           = 'SMK 4 LPPM RI Padalarang';

    // State
    public bool   $sudahAda        = false;
    public ?int   $existingSuratId = null;
    public string $existingNomor   = '';
    public string $existingTanggal = '';
    public bool   $berhasil        = false;
    public int    $suratId         = 0;

    public function mount(int $id): void
    {
        $this->idPelanggaran = $id;

        $pelanggaran = Pelanggaran::with([
            'siswa.kelas',
            'jenisPelanggaran',
        ])->findOrFail($id);

        $this->namaSiswa        = $pelanggaran->siswa->nama ?? '-';
        $this->namaKelas        = $pelanggaran->siswa?->kelas?->nama_kelas ?? '-';
        $this->jenisPelanggaran = $pelanggaran->jenisPelanggaran->nama_pelanggaran ?? '-';
        $this->tingkat          = ucfirst($pelanggaran->jenisPelanggaran->tingkat_pelanggaran ?? '-');
        $this->waktuKejadian    = \Carbon\Carbon::parse($pelanggaran->waktu_kejadian)
            ->translatedFormat('l, d F Y H:i');

        // Cek surat existing
        $existing = SuratPanggilan::where('id_pelanggaran', $id)->first();
        if ($existing) {
            $this->sudahAda        = true;
            $this->existingSuratId = $existing->id_surat;
            $this->existingNomor   = $existing->nomor_surat;
            $this->existingTanggal = \Carbon\Carbon::parse($existing->tanggal_panggilan)
                ->format('d/m/Y');
        }

        // Default tanggal = besok
        $this->tanggalPanggilan = now()->addDay()->format('Y-m-d');
    }

    public function simpan(): void
    {
        $this->validate([
            'tanggalPanggilan' => 'required|date',
            'waktuJam'         => 'required',
            'waktuMenit'       => 'required',
            'tempat'           => 'required|string|max:255',
        ], [
            'tanggalPanggilan.required' => 'Tanggal panggilan wajib diisi.',
            'waktuJam.required'         => 'Jam panggilan wajib dipilih.',
            'waktuMenit.required'       => 'Menit panggilan wajib dipilih.',
        ]);

        $pelanggaran = Pelanggaran::with('siswa.kelas.waliKelas')->findOrFail($this->idPelanggaran);
        $waliKelas   = $pelanggaran->siswa?->kelas?->waliKelas;

        $waktu = $this->waktuJam . ':' . $this->waktuMenit . ':00';

        $surat = SuratPanggilan::create([
            'id_pelanggaran'    => $this->idPelanggaran,
            'id_walikelas'      => $waliKelas?->id_walikelas,
            'nomor_surat'       => SuratPanggilan::generateNomor(),
            'tanggal_panggilan' => $this->tanggalPanggilan,
            'waktu_panggilan'   => $waktu,
            'tempat'            => $this->tempat,
        ]);

        $this->suratId  = $surat->id_surat;
        $this->berhasil = true;
    }
};
?>

<div class="max-w-lg mx-auto p-4 space-y-4">

    {{-- Back --}}
    <div>
        <a href="{{ route('pelanggaran.index') }}" wire:navigate
            class="inline-flex items-center gap-1.5 text-sm text-[#0D2D6B] hover:underline">
            ← Kembali ke Data Pelanggaran
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#0D2D6B] to-[#163580] px-5 py-4">
            <h2 class="text-white font-bold text-base flex items-center gap-2">
                📄 Buat Surat Panggilan Orang Tua
            </h2>
            <p class="text-blue-200 text-xs mt-0.5">
                {{ $namaSiswa }} · {{ $namaKelas }}
            </p>
        </div>

        {{-- Info pelanggaran --}}
        <div class="px-5 py-4 bg-[#f0f4fb] space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center
                            justify-center text-sm flex-shrink-0 shadow-sm">⚠️</div>
                <div>
                    <p class="text-[11px] text-[#4A5E8A] font-semibold uppercase tracking-wide">
                        Jenis Pelanggaran
                    </p>
                    <p class="text-sm font-bold text-[#0D2D6B]">
                        {{ $jenisPelanggaran }}
                        <span class="font-normal text-xs
                            {{ $tingkat === 'Berat' ? 'text-red-600' :
                               ($tingkat === 'Sedang' ? 'text-orange-500' : 'text-yellow-600') }}">
                            ({{ $tingkat }})
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center
                            justify-center text-sm flex-shrink-0 shadow-sm">🕐</div>
                <div>
                    <p class="text-[11px] text-[#4A5E8A] font-semibold uppercase tracking-wide">
                        Waktu Kejadian
                    </p>
                    <p class="text-sm text-[#1e3a6e]">{{ $waktuKejadian }}</p>
                </div>
            </div>
        </div>

        {{-- ── Sudah ada surat ── --}}
        @if ($sudahAda)
            <div class="px-5 py-5 space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-green-700">✅ Surat panggilan sudah pernah dibuat</p>
                    <p class="text-xs text-green-600 mt-1">
                        No: <span class="font-mono font-semibold">{{ $existingNomor }}</span>
                        · Tanggal panggilan: {{ $existingTanggal }}
                    </p>
                </div>
                <a href="{{ route('surat-panggilan.cetak', $existingSuratId) }}"
                    target="_blank"
                    class="flex items-center justify-center gap-2 w-full py-3 rounded-xl
                           bg-gradient-to-r from-[#0D2D6B] to-[#163580] text-white
                           text-sm font-bold hover:from-[#163580] hover:to-[#1e45a0]
                           transition-all duration-200 shadow-md">
                    🖨️ Cetak Ulang PDF
                </a>
            </div>

        {{-- ── Berhasil dibuat ── --}}
        @elseif ($berhasil)
            <div class="px-5 py-5 space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                    <div class="text-3xl mb-2">✅</div>
                    <p class="text-sm font-bold text-green-700">Surat berhasil dibuat!</p>
                    <p class="text-xs text-green-600 mt-1">Klik tombol di bawah untuk membuka PDF</p>
                </div>
                <a href="{{ route('surat-panggilan.cetak', $suratId) }}"
                    target="_blank"
                    class="flex items-center justify-center gap-2 w-full py-3 rounded-xl
                           bg-gradient-to-r from-[#0D2D6B] to-[#163580] text-white
                           text-sm font-bold hover:from-[#163580] hover:to-[#1e45a0]
                           transition-all duration-200 shadow-md">
                    🖨️ Buka & Cetak PDF
                </a>
                <a href="{{ route('pelanggaran.index') }}" wire:navigate
                    class="flex items-center justify-center w-full py-2.5 rounded-xl
                           border border-gray-200 text-sm text-gray-600
                           hover:bg-gray-50 transition-colors">
                    Kembali ke Daftar Pelanggaran
                </a>
            </div>

        {{-- ── Form input ── --}}
        @else
            <div class="px-5 py-5 space-y-4">

                {{-- Tanggal panggilan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Tanggal Panggilan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="tanggalPanggilan"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="w-full h-11 px-3 rounded-xl border text-sm transition-colors
                               @error('tanggalPanggilan') border-red-400 bg-red-50
                               @else border-gray-200 focus:border-[#F5B800] @enderror">
                    @error('tanggalPanggilan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Waktu panggilan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Waktu Panggilan <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <select wire:model="waktuJam"
                            class="flex-1 h-11 px-3 rounded-xl border border-gray-200 text-sm
                                   focus:border-[#F5B800] focus:ring-[#F5B800]">
                            @for ($h = 6; $h <= 17; $h++)
                                <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">
                                    {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                        <span class="text-gray-400 font-bold text-lg">:</span>
                        <select wire:model="waktuMenit"
                            class="flex-1 h-11 px-3 rounded-xl border border-gray-200 text-sm
                                   focus:border-[#F5B800] focus:ring-[#F5B800]">
                            @foreach (['00','15','30','45'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                        <span class="text-sm text-gray-500 flex-shrink-0">WIB</span>
                    </div>
                    @error('waktuJam')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tempat --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tempat</label>
                    <input type="text" wire:model="tempat"
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 text-sm
                               focus:border-[#F5B800] focus:ring-[#F5B800]">
                </div>

                {{-- Tombol --}}
                <div class="flex gap-3 pt-1">
                    <a href="{{ route('pelanggaran.index') }}" wire:navigate
                        class="flex-1 flex items-center justify-center px-4 py-2.5 rounded-xl
                               border border-gray-200 text-sm text-gray-600
                               hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button wire:click="simpan" wire:loading.attr="disabled"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5
                               rounded-xl bg-gradient-to-r from-[#0D2D6B] to-[#163580]
                               text-white text-sm font-bold hover:from-[#163580]
                               hover:to-[#1e45a0] transition-all duration-200
                               disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpan">🖨️ Simpan & Cetak</span>
                        <span wire:loading wire:target="simpan">Menyimpan...</span>
                    </button>
                </div>

            </div>
        @endif

    </div>
</div>