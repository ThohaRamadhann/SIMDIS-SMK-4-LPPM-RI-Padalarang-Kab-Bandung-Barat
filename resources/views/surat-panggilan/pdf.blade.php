<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.5;
            background: #fff;
        }

        .page {
            padding: 15mm 20mm 12mm 25mm;
        }

        /* ===== KOP ===== */
        .kop {
            width: 100%;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo-td {
            width: 95px;
            vertical-align: middle;
            text-align: center;
            padding-right: 6px;
        }

        .kop-logo-td img {
            width: 85px;
            height: auto;
        }

        .kop-text-td {
            text-align: center;
            vertical-align: middle;
        }

        .kop-lembaga {
            font-size: 8pt;
            font-weight: bold;
            color: #7a0000;
            line-height: 1.15;
            margin-bottom: 1px;
        }

        .kop-nama {
            font-size: 25pt;
            font-weight: bold;
            color: #1b3f91;
            line-height: 1;
            margin: 1px 0;
            letter-spacing: 0.3px;
        }

        .kop-jurusan {
            font-size: 6.7pt;
            line-height: 1.15;
            margin-top: 2px;
        }

        .kop-akreditasi {
            font-size: 7.5pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .kop-alamat {
            font-size: 7.8pt;
            line-height: 1.2;
            margin-top: 2px;
        }

        /* ===== GARIS — full bleed melampaui padding .page ===== */
        .garis-kop {
            margin-top: 4px;
            margin-left:  0mm;
            margin-right: 0mm;
        }

        .garis-kop-1 {
            border-top: 3px solid #000;
        }

        .garis-kop-2 {
            border-top: 1px solid #000;
            margin-top: 2px;
        }

        /* ===== HEADER SURAT ===== */
        .header-surat {
            margin-top: 12px;
            margin-bottom: 16px;
        }

        .header-surat table {
            border-collapse: collapse;
            font-size: 11pt;
        }

        .header-surat td {
            padding: 1px 0;
            vertical-align: top;
        }

        .label     { width: 68px; }
        .separator { width: 16px; text-align: center; }

        /* ===== KEPADA ===== */
        .kepada {
            margin-bottom: 14px;
            line-height: 1.6;
            font-size: 11pt;
        }

        /* ===== PARAGRAF ===== */
        .paragraph {
            line-height: 1.7;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        /* ===== JADWAL ===== */
        .jadwal {
            margin-top: 2px;
            margin-bottom: 12px;
        }

        .jadwal table {
            border-collapse: collapse;
            font-size: 11pt;
        }

        .jadwal td {
            padding: 2px 0;
            vertical-align: top;
        }

        .jadwal .label { width: 120px; }

        /* ===== PENUTUP ===== */
        .penutup {
            text-align: justify;
            line-height: 1.7;
            margin-bottom: 12px;
            font-size: 11pt;
        }

        /* ===== TTD ===== */
        .ttd { width: 100%; margin-top: 8px; }

        .ttd table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-kanan {
            width: 42%;
            text-align: center;
            vertical-align: top;
            font-size: 11pt;
        }

        .ttd-kanan p { margin-bottom: 2px; }

        .nama-ttd {
            margin-top: 55px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        /* ===== POTONG ===== */
        .potong {
            border-top: 1px dashed #000;
            margin: 18px 0 12px 0;
        }

        /* ===== BUKTI ===== */
        .bukti { margin-top: 4px; font-size: 10.8pt; }

        .bukti-judul {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .bukti p { line-height: 1.7; }

        .garis {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            height: 10px;
        }

        .ttd-penerima { margin-top: 50px; }

        /* ===== PAGE BREAK ===== */
        .kop, .ttd, .potong, .bukti { page-break-inside: avoid; }
    </style>
</head>
<body>
<div class="page">

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
                    <div class="kop-nama">
                        SMK 4 LPPM RI PADALARANG
                    </div>
                    <div class="kop-jurusan">
                        TEKNIK SEPEDA MOTOR, TEKNIK KOMPUTER JARINGAN,
                        REKAYASA PERANGKAT LUNAK DAN AKOMODASI PERHOTELAN
                    </div>
                    <div class="kop-akreditasi">
                        AKREDITASI A
                    </div>
                    <div class="kop-alamat">
                        Jl. Ga. Manulang No. 132 Telp. (022) 6810404
                        Padalarang Kabupaten Bandung Barat
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== GARIS KOP — full bleed ===== --}}
    <div class="garis-kop">
        <div class="garis-kop-1"></div>
        <div class="garis-kop-2"></div>
    </div>

    {{-- ===== HEADER SURAT ===== --}}
    <div class="header-surat">
        <table>
            <tr>
                <td class="label">Nomor</td>
                <td class="separator">:</td>
                <td>{{ $surat->nomor_surat }}</td>
            </tr>
            <tr>
                <td class="label">Perihal</td>
                <td class="separator">:</td>
                <td>Panggilan Orang Tua/Wali Siswa</td>
            </tr>
        </table>
    </div>

    {{-- ===== KEPADA ===== --}}
    <div class="kepada">
        <p>Kepada Yth.</p>
        <p>Orang Tua/Wali dari :</p>
        <p><strong>{{ $siswa->nama ?? '-' }}</strong></p>
        <p>Kelas : {{ $kelas->nama_kelas ?? '-' }}</p>
        <p>Di Tempat</p>
    </div>

    {{-- ===== ISI ===== --}}
    <div class="paragraph">Dengan hormat,</div>

    <div class="paragraph">
        Sehubungan dengan adanya beberapa hal mengenai putra/i Bapak/Ibu,
        maka kami selaku pihak sekolah mengharapkan kehadiran
        Bapak/Ibu orang tua/wali siswa pada:
    </div>

    {{-- ===== JADWAL ===== --}}
    <div class="jadwal">
        <table>
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td class="separator">:</td>
                <td>{{ $hariTanggal }}</td>
            </tr>
            <tr>
                <td class="label">Waktu</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($surat->waktu_panggilan)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Tempat</td>
                <td class="separator">:</td>
                <td>{{ $surat->tempat }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== PENUTUP ===== --}}
    <div class="penutup">
        Mengingat pentingnya hal tersebut, kami mengharapkan
        kehadiran Bapak/Ibu Orang Tua/Wali tepat pada waktunya.
        Demikian surat pemberitahuan ini kami sampaikan,
        atas perhatian dan kerja samanya kami ucapkan terima kasih.
    </div>

    {{-- ===== TTD ===== --}}
    <div class="ttd">
        <table>
            <tr>
                <td width="55%"></td>
                <td class="ttd-kanan">
                    <p>Bandung Barat, {{ $tanggalSurat }}</p>
                    <p>Mengetahui,</p>
                    <p>Wali Kelas</p>
                    <div class="nama-ttd">{{ $namaWaliKelas }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== GARIS POTONG ===== --}}
    <div class="potong"></div>

    {{-- ===== BUKTI PENERIMAAN ===== --}}
    <div class="bukti">
        <div class="bukti-judul">BUKTI PENERIMAAN SURAT PANGGILAN</div>
        <p>
            Surat panggilan orang tua ini telah diterima pada tanggal :
            <span class="garis"></span>
        </p>
        <br><br>
        <p>Bandung Barat, ................. 20....</p>
        <p>Penerima</p>
        <div class="ttd-penerima">(........................................)</div>
    </div>

</div>
</body>
</html>