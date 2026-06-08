<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Pelanggaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #111;
            background: #fff;
            padding: 20px 30px;
        }

        /* ── KOP SURAT ── */
        .kop {
            border-bottom: 3px double #0D2D6B;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo-td {
            width: 80px;
            text-align: center;
            vertical-align: middle;
            padding-right: 12px;
        }

        .kop-logo-td img {
            width: 70px;
            height: auto;
        }

        .kop-text-td {
            vertical-align: middle;
            text-align: center;
        }

        .kop-lembaga {
            font-size: 10px;
            font-weight: normal;
            letter-spacing: 0.02em;
            color: #333;
        }

        .kop-nama {
            font-size: 17px;
            font-weight: bold;
            color: #0D2D6B;
            margin: 3px 0 2px;
            letter-spacing: 0.03em;
        }

        .kop-jurusan {
            font-size: 9px;
            color: #444;
            margin-bottom: 2px;
        }

        .kop-akreditasi {
            font-size: 9.5px;
            font-weight: bold;
            color: #0D2D6B;
            margin-bottom: 2px;
        }

        .kop-alamat {
            font-size: 9px;
            color: #555;
        }

        /* ── JUDUL LAPORAN ── */
        .judul-wrap {
            text-align: center;
            margin-bottom: 12px;
        }

        .judul-laporan {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #0D2D6B;
        }

        .judul-sub {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
        }
        .meta-label {
            font-weight: bold;
            color: #0D2D6B;
        }

        .filter-pill {
            display: inline-block;
            background: #eef3fc;
            color: #0D2D6B;
            border: 1px solid #c5d5ee;
            border-radius: 4px;
            padding: 1px 7px;
            font-size: 9px;
            font-weight: bold;
            margin-right: 4px;
        }

        /* ── TABEL ── */
        .tabel-pelanggaran {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .tabel-pelanggaran thead tr {
            background: #0D2D6B;
            color: #fff;
        }

        .tabel-pelanggaran thead th {
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 8.5px;
            letter-spacing: 0.03em;
            border: 1px solid #0a2458;
        }

        .tabel-pelanggaran thead th.text-left {
            text-align: left;
        }

        .tabel-pelanggaran tbody tr:nth-child(even) {
            background: #f4f8ff;
        }

        .tabel-pelanggaran tbody tr:nth-child(odd) {
            background: #fff;
        }

        .tabel-pelanggaran tbody td {
            padding: 5px 5px;
            border: 1px solid #d8e4f5;
            vertical-align: middle;
            color: #222;
        }

        .tabel-pelanggaran tbody td.center {
            text-align: center;
        }

        /* Badge tingkat */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-ringan  { background: #d1fae5; color: #065f46; }
        .badge-sedang  { background: #fef3c7; color: #92400e; }
        .badge-berat   { background: #fee2e2; color: #991b1b; }
        .badge-belum   { background: #fee2e2; color: #991b1b; }
        .badge-proses  { background: #fef3c7; color: #92400e; }
        .badge-selesai { background: #d1fae5; color: #065f46; }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .footer-flex {
            width: 100%;
        }

        .footer-left {
            width: 60%;
            vertical-align: top;
        }

        .footer-right {
            width: 40%;
            text-align: center;
            vertical-align: top;
        }

        .ttd-nama {
            font-size: 10.5px;
            font-weight: bold;
            color: #0D2D6B;
            border-top: 1px solid #0D2D6B;
            display: inline-block;
            padding-top: 4px;
            min-width: 160px;
        }

        .ttd-jabatan {
            font-size: 9.5px;
            color: #555;
        }
    </style>
</head>
<body>

    {{-- ===== KOP SURAT ===== --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-td">
                    <img src="{{ public_path('images/logo_lppm.png') }}" alt="Logo LPPM">
                </td>
                <td class="kop-text-td">
                    <div class="kop-lembaga">
                        LEMBAGA PENDIDIKAN DAN PENGETAHUAN MASYARAKAT REPUBLIK INDONESIA
                    </div>
                    <div class="kop-nama">SMK 4 LPPM RI PADALARANG</div>
                    <div class="kop-jurusan">
                        TEKNIK SEPEDA MOTOR, TEKNIK KOMPUTER JARINGAN,
                        REKAYASA PERANGKAT LUNAK DAN AKOMODASI PERHOTELAN
                    </div>
                    <div class="kop-akreditasi">AKREDITASI A</div>
                    <div class="kop-alamat">
                        Jl. Ga. Manulang No. 132 Telp. (022) 6810404
                        Padalarang Kabupaten Bandung Barat
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== JUDUL ===== --}}
    <div class="judul-wrap">
        <div class="judul-laporan">Laporan Data Pelanggaran Siswa</div>
        <div class="judul-sub">Sistem Informasi Manajemen Disiplin Siswa (SIMDIS)</div>
    </div>

    {{-- ===== META INFO ===== --}}
    <table style="width:100%;margin-bottom:12px;font-size:10px;">
        <tr>
            <td style="vertical-align:top;width:55%;">
                <span class="meta-label">Tanggal Cetak &nbsp;:</span>
                {{ $tanggalCetak }}, {{ $jamCetak }}<br>
                <span class="meta-label">Total Data &nbsp;&nbsp;&nbsp;&nbsp;:</span>
                {{ $pelanggarans->count() }} pelanggaran
                @if(count($filterInfo) > 0)
                    <br>
                    <span class="meta-label">Filter Aktif &nbsp;&nbsp;:</span>
                    @foreach($filterInfo as $fi)
                        <span class="filter-pill">{{ $fi }}</span>
                    @endforeach
                @endif
            </td>
            <td style="vertical-align:top;width:45%;text-align:right;">
                @php
                    $ringan  = $pelanggarans->filter(fn($p) => ($p->jenisPelanggaran->tingkat_pelanggaran ?? '') === 'Ringan')->count();
                    $sedang  = $pelanggarans->filter(fn($p) => ($p->jenisPelanggaran->tingkat_pelanggaran ?? '') === 'Sedang')->count();
                    $berat   = $pelanggarans->filter(fn($p) => ($p->jenisPelanggaran->tingkat_pelanggaran ?? '') === 'Berat')->count();
                    $belum   = $pelanggarans->where('status_pembinaan', 'Belum Ditindak')->count();
                    $proses  = $pelanggarans->where('status_pembinaan', 'Dalam Proses')->count();
                    $selesai = $pelanggarans->where('status_pembinaan', 'Selesai')->count();
                @endphp
                <table style="display:inline-table;border-collapse:collapse;font-size:9.5px;">
                    <tr>
                        <td style="padding:2px 10px;border:1px solid #d8e4f5;background:#f4f8ff;">
                            <span style="color:#065f46;font-weight:bold;">Ringan: {{ $ringan }}</span>
                        </td>
                        <td style="padding:2px 10px;border:1px solid #d8e4f5;background:#f4f8ff;">
                            <span style="color:#92400e;font-weight:bold;">Sedang: {{ $sedang }}</span>
                        </td>
                        <td style="padding:2px 10px;border:1px solid #d8e4f5;background:#f4f8ff;">
                            <span style="color:#991b1b;font-weight:bold;">Berat: {{ $berat }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:2px 10px;border:1px solid #d8e4f5;">
                            <span style="color:#991b1b;">Belum Ditindak: {{ $belum }}</span>
                        </td>
                        <td style="padding:2px 10px;border:1px solid #d8e4f5;">
                            <span style="color:#92400e;">Dalam Proses: {{ $proses }}</span>
                        </td>
                        <td style="padding:2px 10px;border:1px solid #d8e4f5;">
                            <span style="color:#065f46;">Selesai: {{ $selesai }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== TABEL DATA ===== --}}
    @if($pelanggarans->isEmpty())
        <p class="no-data">Tidak ada data pelanggaran yang sesuai dengan filter.</p>
    @else
        <table class="tabel-pelanggaran">
            <thead>
                <tr>
                    <th style="width:24px;">No</th>
                    <th class="text-left" style="width:110px;">Nama Siswa</th>
                    <th style="width:60px;">NIS</th>
                    <th style="width:55px;">Kelas</th>
                    <th style="width:80px;">Jurusan</th>
                    <th style="width:60px;">Tahun Ajaran</th>
                    <th class="text-left" style="width:100px;">Wali Kelas</th>
                    <th class="text-left" style="width:120px;">Jenis Pelanggaran</th>
                    <th style="width:48px;">Tingkat</th>
                    <th style="width:48px;">Status</th>
                    <th style="width:80px;">Waktu Kejadian</th>
                    <th style="width:75px;">Tgl Pembinaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelanggarans as $i => $p)
                    @php
                        $tingkat = $p->jenisPelanggaran->tingkat_pelanggaran ?? '-';
                        $status  = $p->status_pembinaan ?? '-';

                        $badgeTingkat = match($tingkat) {
                            'Ringan' => 'badge-ringan',
                            'Sedang' => 'badge-sedang',
                            'Berat'  => 'badge-berat',
                            default  => '',
                        };

                        $badgeStatus = match($status) {
                            'Belum Ditindak' => 'badge-belum',
                            'Dalam Proses'   => 'badge-proses',
                            'Selesai'        => 'badge-selesai',
                            default          => '',
                        };
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $p->siswa->nama ?? '-' }}</td>
                        <td class="center">{{ $p->siswa->nis ?? '-' }}</td>
                        <td class="center">{{ $p->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                        <td class="center">{{ $p->siswa?->kelas?->jurusan ?? '-' }}</td>
                        <td class="center">{{ $p->siswa?->kelas?->tahun_ajaran ?? '-' }}</td>
                        <td>{{ $p->waliKelas?->pengguna?->name ?? '-' }}</td>
                        <td>{{ $p->jenisPelanggaran->nama_pelanggaran ?? '-' }}</td>
                        <td class="center">
                            <span class="badge {{ $badgeTingkat }}">{{ $tingkat }}</span>
                        </td>
                        <td class="center">
                            <span class="badge {{ $badgeStatus }}">
                                @if($status === 'Belum Ditindak') Belum
                                @elseif($status === 'Dalam Proses') Proses
                                @else {{ $status }}
                                @endif
                            </span>
                        </td>
                        <td class="center">
                            {{ \Carbon\Carbon::parse($p->waktu_kejadian)->format('d/m/Y H:i') }}
                        </td>
                        <td class="center">
                            @if($p->tanggal_pembinaan)
                                {{ \Carbon\Carbon::parse($p->tanggal_pembinaan)->format('d/m/Y') }}
                                @if($p->jam_pembinaan)
                                    <br><span style="color:#666;">{{ \Carbon\Carbon::parse($p->jam_pembinaan)->format('H:i') }}</span>
                                @endif
                            @else
                                <span style="color:#bbb;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ===== FOOTER / TTD ===== --}}
    <table class="footer-flex" style="margin-top:24px;">
        <tr>
            <td class="footer-left">
                <div style="font-size:9.5px;color:#888;line-height:1.8;">
                    * Dokumen ini dicetak secara otomatis oleh sistem SIMDIS.<br>
                    * Data yang ditampilkan sesuai dengan filter yang dipilih saat cetak.
                </div>
            </td>
            <td class="footer-right">
                <div style="font-size:10px;color:#333;">
                    Padalarang, {{ now()->translatedFormat('d F Y') }}<br>
                    Guru Bimbingan Konseling
                </div>
                <br><br><br><br>
                <div class="ttd-nama">_________________________</div><br>
                <div class="ttd-jabatan">NIP. .................................</div>
            </td>
        </tr>
    </table>

</body>
</html>