<?php

use App\Models\Pelanggaran;
use App\Models\SuratPanggilan;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    public int $idPelanggaran;

    public string $namaSiswa        = '';
    public string $namaKelas        = '';
    public string $jenisPelanggaran = '';
    public string $tingkat          = '';
    public string $waktuKejadian    = '';

    public string $tanggalPanggilan = '';
    public string $waktuJam         = '08';
    public string $waktuMenit       = '00';
    public string $tempat           = 'SMK 4 LPPM RI Padalarang';

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

        $existing = SuratPanggilan::where('id_pelanggaran', $id)->first();
        if ($existing) {
            $this->sudahAda        = true;
            $this->existingSuratId = $existing->id_surat;
            $this->existingNomor   = $existing->nomor_surat;
            $this->existingTanggal = \Carbon\Carbon::parse($existing->tanggal_panggilan)
                ->format('d/m/Y');
        }

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
        $waktu       = $this->waktuJam . ':' . $this->waktuMenit . ':00';

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

<div class="max-w-xl mx-auto px-4 py-6 space-y-5">

    <style>
        .sp-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 16px 0 rgba(13,45,107,0.08), 0 1px 3px rgba(13,45,107,0.06);
            border: 1px solid #e8eef7;
            overflow: hidden;
        }

        .sp-header {
            position: relative;
            background: linear-gradient(135deg, #0D2D6B 0%, #163580 60%, #1a3d8f 100%);
            padding: 24px 24px 20px;
            overflow: hidden;
        }

        .sp-header::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .sp-header::after {
            content: '';
            position: absolute;
            bottom: -20px; left: 40px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(245,184,0,0.12);
        }

        .sp-badge-tingkat {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.04em;
        }

        .sp-badge-berat   { background: #fee2e2; color: #b91c1c; }
        .sp-badge-sedang  { background: #fef3c7; color: #b45309; }
        .sp-badge-ringan  { background: #d1fae5; color: #065f46; }

        .sp-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 20px 24px;
            background: #f6f9ff;
            border-bottom: 1px solid #e8eef7;
        }

        .sp-info-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sp-info-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b84b8;
        }

        .sp-info-value {
            font-size: 13px;
            font-weight: 600;
            color: #0D2D6B;
            line-height: 1.4;
        }

        .sp-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e8eef7, transparent);
            margin: 0 24px;
        }

        .sp-form-body {
            padding: 20px 24px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .sp-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #4a5e8a;
            margin-bottom: 6px;
            letter-spacing: 0.03em;
        }

        .sp-input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1.5px solid #dde5f5;
            font-size: 13.5px;
            color: #1e3a6e;
            background: #f8faff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            box-sizing: border-box;
        }

        .sp-input:focus {
            border-color: #F5B800;
            box-shadow: 0 0 0 3px rgba(245,184,0,0.12);
            background: #fff;
        }

        .sp-input.error {
            border-color: #f87171;
            background: #fff5f5;
        }

        .sp-time-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sp-time-wrap .sp-input {
            flex: 1;
        }

        .sp-time-sep {
            font-size: 18px;
            font-weight: 700;
            color: #9db4d8;
            flex-shrink: 0;
        }

        .sp-time-suffix {
            font-size: 12px;
            font-weight: 600;
            color: #6b84b8;
            flex-shrink: 0;
            background: #eef3fc;
            padding: 0 10px;
            height: 44px;
            display: flex;
            align-items: center;
            border-radius: 10px;
        }

        .sp-error-msg {
            font-size: 11px;
            color: #ef4444;
            margin-top: 4px;
        }

        .sp-btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            border-radius: 13px;
            background: linear-gradient(135deg, #0D2D6B, #1a3d8f);
            color: #fff;
            font-size: 13.5px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            box-shadow: 0 4px 14px rgba(13,45,107,0.28);
            text-decoration: none;
        }

        .sp-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13,45,107,0.36);
        }

        .sp-btn-primary:active {
            transform: translateY(0);
        }

        .sp-btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .sp-btn-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 13px 20px;
            border-radius: 13px;
            background: #fff;
            color: #4a5e8a;
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid #dde5f5;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
        }

        .sp-btn-secondary:hover {
            background: #f6f9ff;
            border-color: #bbd0f5;
        }

        .sp-actions {
            display: flex;
            gap: 10px;
        }

        .sp-actions .sp-btn-secondary {
            flex: 0 0 auto;
            width: auto;
            padding: 13px 18px;
        }

        .sp-actions .sp-btn-primary {
            flex: 1;
        }

        /* Success / Already exists state */
        .sp-status-box {
            margin: 20px 24px 0;
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .sp-status-box.success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1.5px solid #6ee7b7;
        }

        .sp-status-box.warning {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1.5px solid #fcd34d;
        }

        .sp-status-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sp-status-icon.green { background: #a7f3d0; }
        .sp-status-icon.yellow { background: #fde68a; }

        .sp-nomor-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(13,45,107,0.08);
            color: #0D2D6B;
            font-size: 11px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            padding: 3px 10px;
            border-radius: 6px;
            margin-top: 4px;
        }

        .sp-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #4a5e8a;
            text-decoration: none;
            padding: 8px 14px 8px 10px;
            border-radius: 10px;
            background: #fff;
            border: 1.5px solid #dde5f5;
            transition: background 0.15s, color 0.15s;
        }

        .sp-back-link:hover {
            background: #f0f5ff;
            color: #0D2D6B;
        }

        .sp-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9db4d8;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sp-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e8eef7;
        }

        /* Spinner */
        .sp-spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: sp-spin 0.7s linear infinite;
            display: inline-block;
        }

        @keyframes sp-spin { to { transform: rotate(360deg); } }

        /* Step indicator */
        .sp-step-bar {
            display: flex;
            align-items: center;
            gap: 0;
            padding: 16px 24px 0;
        }

        .sp-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
            position: relative;
        }

        .sp-step-dot {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            border: 2px solid #dde5f5;
            background: #fff;
            color: #9db4d8;
            z-index: 1;
            position: relative;
        }

        .sp-step-dot.active {
            background: #0D2D6B;
            border-color: #0D2D6B;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(13,45,107,0.12);
        }

        .sp-step-dot.done {
            background: #10b981;
            border-color: #10b981;
            color: #fff;
        }

        .sp-step-line {
            flex: 1;
            height: 2px;
            background: #dde5f5;
            margin-top: -14px;
        }

        .sp-step-line.done { background: #10b981; }

        .sp-step-text {
            font-size: 10px;
            font-weight: 600;
            color: #9db4d8;
            text-align: center;
        }

        .sp-step-text.active { color: #0D2D6B; }
        .sp-step-text.done   { color: #10b981; }
    </style>

    {{-- Back link --}}
    <a href="{{ route('pelanggaran.index') }}" wire:navigate class="sp-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Kembali ke Data Pelanggaran
    </a>

    <div class="sp-card">

        {{-- Header --}}
        <div class="sp-header">
            <div style="position:relative; z-index:1;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.15);
                                display:flex;align-items:center;justify-content:center;font-size:20px;">
                        📄
                    </div>
                    <div>
                        <h2 style="color:#fff;font-size:15px;font-weight:800;margin:0;line-height:1.2;">
                            Surat Panggilan Orang Tua
                        </h2>
                        <p style="color:#93b4e8;font-size:11px;margin:2px 0 0;">
                            SIMDIS · SMK 4 LPPM RI Padalarang
                        </p>
                    </div>
                </div>

                <div style="background:rgba(255,255,255,0.1);border-radius:10px;padding:10px 14px;">
                    <div style="font-size:10px;color:#93b4e8;font-weight:600;
                                text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px;">Siswa</div>
                    <div style="font-size:15px;color:#fff;font-weight:700;line-height:1.3;word-break:break-word;">
                        {{ $namaSiswa }}
                    </div>
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,0.12);">
                        <div style="font-size:10px;color:#93b4e8;font-weight:600;
                                    text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Kelas</div>
                        <div style="font-size:13px;color:#F5B800;font-weight:700;">
                            {{ $namaKelas }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step indicator --}}
        @php
            $step1 = $berhasil || $sudahAda ? 'done' : 'active';
            $step2 = $berhasil ? 'done' : ($sudahAda ? 'done' : '');
            $step3 = $berhasil || $sudahAda ? 'done' : '';
            $line1 = $berhasil || $sudahAda ? 'done' : '';
            $line2 = $berhasil ? 'done' : '';
        @endphp
        <div class="sp-step-bar">
            <div class="sp-step">
                <div class="sp-step-dot {{ $step1 }}">
                    @if($step1 === 'done') ✓ @else 1 @endif
                </div>
                <div class="sp-step-text {{ $step1 }}">Detail</div>
            </div>
            <div class="sp-step-line {{ $line1 }}"></div>
            <div class="sp-step">
                <div class="sp-step-dot {{ $step2 }}">
                    @if($step2 === 'done') ✓ @else 2 @endif
                </div>
                <div class="sp-step-text {{ $step2 }}">Jadwal</div>
            </div>
            <div class="sp-step-line {{ $line2 }}"></div>
            <div class="sp-step">
                <div class="sp-step-dot {{ $step3 }}">
                    @if($step3 === 'done') ✓ @else 3 @endif
                </div>
                <div class="sp-step-text {{ $step3 }}">Cetak</div>
            </div>
        </div>

        {{-- Info pelanggaran --}}
        <div class="sp-info-grid">
            <div class="sp-info-item" style="grid-column: span 2;">
                <div class="sp-info-label">Jenis Pelanggaran</div>
                <div class="sp-info-value" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    {{ $jenisPelanggaran }}
                    <span class="sp-badge-tingkat
                        {{ $tingkat === 'Berat' ? 'sp-badge-berat' : ($tingkat === 'Sedang' ? 'sp-badge-sedang' : 'sp-badge-ringan') }}">
                        @if($tingkat === 'Berat') 🔴 @elseif($tingkat === 'Sedang') 🟡 @else 🟢 @endif
                        {{ $tingkat }}
                    </span>
                </div>
            </div>
            <div class="sp-info-item" style="grid-column: span 2;">
                <div class="sp-info-label">Waktu Kejadian</div>
                <div class="sp-info-value" style="font-weight:500;color:#374151;">{{ $waktuKejadian }}</div>
            </div>
        </div>

        {{-- ── Sudah ada surat ── --}}
        @if ($sudahAda)
            <div style="padding: 20px 24px 24px; display:flex; flex-direction:column; gap:14px;">

                <div class="sp-status-box warning">
                    <div class="sp-status-icon yellow">📋</div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#92400e;">
                            Surat sudah pernah dibuat
                        </div>
                        <div style="font-size:12px;color:#b45309;margin-top:3px;">
                            Tanggal panggilan: <strong>{{ $existingTanggal }}</strong>
                        </div>
                        <div class="sp-nomor-pill">{{ $existingNomor }}</div>
                    </div>
                </div>

                <a href="{{ route('surat-panggilan.cetak', $existingSuratId) }}"
                    target="_blank" class="sp-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8" rx="1"/>
                    </svg>
                    Cetak Ulang PDF
                </a>

                <a href="{{ route('pelanggaran.index') }}" wire:navigate class="sp-btn-secondary">
                    Kembali ke Daftar
                </a>
            </div>

        {{-- ── Berhasil dibuat ── --}}
        @elseif ($berhasil)
            <div style="padding: 20px 24px 24px; display:flex; flex-direction:column; gap:14px;">

                <div class="sp-status-box success">
                    <div class="sp-status-icon green">✅</div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#065f46;">
                            Surat berhasil dibuat!
                        </div>
                        <div style="font-size:12px;color:#047857;margin-top:3px;">
                            Klik tombol di bawah untuk membuka dan mencetak PDF.
                        </div>
                    </div>
                </div>

                <a href="{{ route('surat-panggilan.cetak', $suratId) }}"
                    target="_blank" class="sp-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8" rx="1"/>
                    </svg>
                    Buka &amp; Cetak PDF
                </a>

                <a href="{{ route('pelanggaran.index') }}" wire:navigate class="sp-btn-secondary">
                    Kembali ke Daftar Pelanggaran
                </a>
            </div>

        {{-- ── Form input ── --}}
        @else
            <div class="sp-form-body">

                <div class="sp-section-title">Jadwal Panggilan</div>

                {{-- Tanggal --}}
                <div>
                    <label class="sp-label">
                        Tanggal Panggilan <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="date" wire:model="tanggalPanggilan"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="sp-input @error('tanggalPanggilan') error @enderror">
                    @error('tanggalPanggilan')
                        <p class="sp-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Waktu --}}
                <div>
                    <label class="sp-label">
                        Waktu Panggilan <span style="color:#ef4444;">*</span>
                    </label>
                    <div class="sp-time-wrap">
                        <select wire:model="waktuJam"
                            class="sp-input @error('waktuJam') error @enderror">
                            @for ($h = 6; $h <= 17; $h++)
                                <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">
                                    {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                        <span class="sp-time-sep">:</span>
                        <select wire:model="waktuMenit"
                            class="sp-input @error('waktuMenit') error @enderror">
                            @foreach (['00','15','30','45'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                        <span class="sp-time-suffix">WIB</span>
                    </div>
                    @error('waktuJam')
                        <p class="sp-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tempat --}}
                <div>
                    <label class="sp-label">Tempat</label>
                    <input type="text" wire:model="tempat"
                        class="sp-input @error('tempat') error @enderror"
                        placeholder="Masukkan lokasi pertemuan">
                    @error('tempat')
                        <p class="sp-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="sp-actions" style="margin-top:4px;">
                    <a href="{{ route('pelanggaran.index') }}" wire:navigate class="sp-btn-secondary">
                        Batal
                    </a>
                    <button wire:click="simpan" wire:loading.attr="disabled" class="sp-btn-primary">
                        <span wire:loading.remove wire:target="simpan"
                              style="display:flex;align-items:center;gap:8px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5">
                                <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                                <rect x="6" y="14" width="12" height="8" rx="1"/>
                            </svg>
                            Simpan &amp; Cetak
                        </span>
                        <span wire:loading wire:target="simpan"
                              style="display:none;align-items:center;gap:8px;">
                            <span class="sp-spinner"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </div>
        @endif

    </div>
</div>