<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold text-[#0D2D6B]">
            Dashboard
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        .dash-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.35rem 1.5rem;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .dash-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(13, 45, 107, 0.13);
        }

        .dash-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .dash-card-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #4A5E8A;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.2rem;
        }

        .dash-card-value {
            font-size: 1.9rem;
            font-weight: 800;
            color: #0D2D6B;
            line-height: 1;
        }

        .dash-card-sub {
            font-size: 0.72rem;
            color: #718096;
            margin-top: 0.25rem;
        }

        .chart-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
        }

        .chart-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0D2D6B;
            margin-bottom: 0.2rem;
        }

        .chart-subtitle {
            font-size: 0.72rem;
            color: #718096;
            margin-bottom: 1rem;
        }

        .section-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #4A5E8A;
            margin-bottom: 0.75rem;
        }

        .trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
        }

        .trend-up {
            background: #ffe4e4;
            color: #c53030;
        }

        .trend-down {
            background: #e6ffed;
            color: #276749;
        }

        .trend-same {
            background: #f0f4fb;
            color: #4A5E8A;
        }

        /* ── Kelengkapan Data ── */
        .kelengkapan-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
            overflow: hidden;
            transition: box-shadow 0.18s ease;
        }

        .kelengkapan-card:hover {
            box-shadow: 0 6px 20px rgba(13, 45, 107, 0.13);
        }

        .kelengkapan-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.1rem;
            cursor: pointer;
            user-select: none;
            gap: 0.75rem;
        }

        .kelengkapan-header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 0;
        }

        .kelengkapan-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .kelengkapan-judul {
            font-size: 0.83rem;
            font-weight: 700;
            color: #1a202c;
            line-height: 1.3;
        }

        .kelengkapan-sub {
            font-size: 0.7rem;
            color: #718096;
            margin-top: 0.1rem;
        }

        .kelengkapan-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .kelengkapan-chevron {
            flex-shrink: 0;
            transition: transform 0.2s ease;
            color: #a0aec0;
            font-size: 0.75rem;
        }

        .kelengkapan-card.open .kelengkapan-chevron {
            transform: rotate(180deg);
        }

        .kelengkapan-body {
            display: none;
            border-top: 1px solid #f0f4fb;
            padding: 0.75rem 1.1rem 1rem;
        }

        .kelengkapan-card.open .kelengkapan-body {
            display: block;
        }

        .kelengkapan-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0;
            font-size: 0.8rem;
            color: #2d3748;
            border-bottom: 1px dashed #f0f4fb;
        }

        .kelengkapan-item:last-child {
            border-bottom: none;
        }

        .kelengkapan-item::before {
            content: '•';
            color: #a0aec0;
            flex-shrink: 0;
        }

        .kelengkapan-more {
            font-size: 0.72rem;
            color: #718096;
            margin-top: 0.5rem;
            font-style: italic;
        }

        .kelengkapan-link {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0D2D6B;
            text-decoration: none;
            padding: 0.3rem 0.75rem;
            border: 1px solid rgba(13, 45, 107, 0.2);
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }

        .kelengkapan-link:hover {
            background: #0D2D6B;
            color: #fff;
        }

        /* Tipe warna */
        .tipe-warning .kelengkapan-icon-wrap {
            background: #fff8e1;
        }

        .tipe-warning .kelengkapan-badge {
            background: #fff8e1;
            color: #b7791f;
        }

        .tipe-warning .kelengkapan-header {
            border-left: 4px solid #F5B800;
        }

        .tipe-danger .kelengkapan-icon-wrap {
            background: #fff0f0;
        }

        .tipe-danger .kelengkapan-badge {
            background: #fff0f0;
            color: #c53030;
        }

        .tipe-danger .kelengkapan-header {
            border-left: 4px solid #fc8181;
        }

        .tipe-info .kelengkapan-icon-wrap {
            background: #ebf8ff;
        }

        .tipe-info .kelengkapan-badge {
            background: #ebf8ff;
            color: #2b6cb0;
        }

        .tipe-info .kelengkapan-header {
            border-left: 4px solid #63b3ed;
        }

        /* Semua data lengkap */
        .kelengkapan-ok {
            background: #fff;
            border-radius: 14px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
        }
    </style>

    <div class="space-y-6">

        {{-- ── GREETING ── --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#4A5E8A]">Selamat datang kembali,</p>
                <h3 class="text-xl font-bold text-[#0D2D6B]">{{ auth()->user()->name }} 👋</h3>
            </div>
            <span class="text-xs text-[#718096] hidden sm:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        {{-- ══════════════════════════════════════════════════
             ADMIN
        ══════════════════════════════════════════════════ --}}
        @if ($role === 'admin')

            {{-- Stats Cards --}}
            <div>
                <p class="section-label">Ringkasan Master Data</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4">

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#e8f0fe">👥</div>
                        <div>
                            <div class="dash-card-label">Total Pengguna</div>
                            <div class="dash-card-value">{{ $stats['total_pengguna'] }}</div>
                            <div class="dash-card-sub">akun terdaftar</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#fce8ff">🎓</div>
                        <div>
                            <div class="dash-card-label">Total Siswa</div>
                            <div class="dash-card-value">{{ $stats['total_siswa'] }}</div>
                            <div class="dash-card-sub">siswa terdaftar</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#e6ffed">🏫</div>
                        <div>
                            <div class="dash-card-label">Total Kelas</div>
                            <div class="dash-card-value">{{ $stats['total_kelas'] }}</div>
                            <div class="dash-card-sub">kelas aktif</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#fff5e6">👨‍👩‍👧</div>
                        <div>
                            <div class="dash-card-label">Total Wali Murid</div>
                            <div class="dash-card-value">{{ $stats['total_walimurid'] }}</div>
                            <div class="dash-card-sub">orang tua terdaftar</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#f0e8ff">👨‍🏫</div>
                        <div>
                            <div class="dash-card-label">Total Wali Kelas</div>
                            <div class="dash-card-value">{{ $stats['total_walikelas'] }}</div>
                            <div class="dash-card-sub">wali kelas aktif</div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Status Kelengkapan Data --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="section-label" style="margin-bottom:0">Status Kelengkapan Data</p>
                    @if (count($kelengkapan) > 0)
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-600">
                            {{ count($kelengkapan) }} masalah ditemukan
                        </span>
                    @else
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-50 text-green-600">
                            ✓ Semua data lengkap
                        </span>
                    @endif
                </div>

                @if (count($kelengkapan) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($kelengkapan as $i => $item)
                            <div class="kelengkapan-card tipe-{{ $item['tipe'] }}" id="card-{{ $i }}">

                                {{-- Header (klik untuk expand) --}}
                                <div class="kelengkapan-header" onclick="toggleKelengkapan({{ $i }})">
                                    <div class="kelengkapan-header-left">
                                        <div class="kelengkapan-icon-wrap">{{ $item['icon'] }}</div>
                                        <div>
                                            <div class="kelengkapan-judul">{{ $item['judul'] }}</div>
                                            <div class="kelengkapan-sub">Klik untuk lihat detail</div>
                                        </div>
                                    </div>
                                    <span class="kelengkapan-badge">{{ $item['jumlah'] }} data</span>
                                    <span class="kelengkapan-chevron">▼</span>
                                </div>

                                {{-- Body (detail) --}}
                                <div class="kelengkapan-body">
                                    @foreach ($item['detail'] as $baris)
                                        <div class="kelengkapan-item">{{ $baris }}</div>
                                    @endforeach

                                    @if ($item['ada_lagi'] > 0)
                                        <p class="kelengkapan-more">
                                            + {{ $item['ada_lagi'] }} data lainnya tidak ditampilkan
                                        </p>
                                    @endif

                                    <a href="{{ $item['link'] }}" class="kelengkapan-link">
                                        Kelola Data →
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="kelengkapan-ok">
                        <div style="font-size:2.5rem;margin-bottom:.5rem">✅</div>
                        <p style="font-weight:700;color:#276749;font-size:.9rem">Semua Data Sudah Lengkap</p>
                        <p style="font-size:.75rem;color:#718096;margin-top:.25rem">
                            Tidak ada data yang bermasalah saat ini.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Charts Admin --}}
            <div>
                <p class="section-label">Grafik & Analisis</p>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

                    <div class="chart-card">
                        <div class="chart-title">Pertumbuhan Pengguna</div>
                        <div class="chart-subtitle">6 bulan terakhir</div>
                        <div id="chartPenggunaBulanan"></div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-title">Distribusi Siswa per Kelas</div>
                        <div class="chart-subtitle">Jumlah siswa di setiap kelas</div>
                        <div id="chartSiswaKelas"></div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
             ORANG TUA
        ══════════════════════════════════════════════════ --}}
        @elseif ($role === 'orang_tua')
            <div>
                <p class="section-label">Ringkasan Pelanggaran Anak</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#fce8e8">📋</div>
                        <div>
                            <div class="dash-card-label">Total Pelanggaran</div>
                            <div class="dash-card-value">{{ $stats['total_pelanggaran'] }}</div>
                            <div class="dash-card-sub">pelanggaran anak Anda</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#e6ffed">✅</div>
                        <div>
                            <div class="dash-card-label">Sudah Ditindak</div>
                            <div class="dash-card-value" style="color:#276749">{{ $stats['sudah_ditindak'] }}</div>
                            <div class="dash-card-sub">sudah mendapat pembinaan</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#fff5e6">⏳</div>
                        <div>
                            <div class="dash-card-label">Belum Ditindak</div>
                            <div class="dash-card-value" style="color:#c05621">{{ $stats['belum_ditindak'] }}</div>
                            <div class="dash-card-sub">menunggu tindakan</div>
                        </div>
                    </div>

                </div>
            </div>

            <div>
                <p class="section-label">Grafik & Analisis</p>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div class="chart-card">
                        <div class="flex items-start justify-between flex-wrap gap-2">
                            <div>
                                <div class="chart-title">Tren Pelanggaran Per Bulan</div>
                                <div class="chart-subtitle">12 bulan terakhir</div>
                            </div>
                            @php
                                $d = $charts['bulanan']['data'];
                                $last = end($d);
                                $prev = count($d) >= 2 ? $d[count($d) - 2] : 0;
                                $diff = $last - $prev;
                            @endphp
                            <span
                                class="trend-badge {{ $diff > 0 ? 'trend-up' : ($diff < 0 ? 'trend-down' : 'trend-same') }}">
                                @if ($diff > 0)
                                    ▲ Naik {{ $diff }}
                                @elseif ($diff < 0)
                                    ▼ Turun {{ abs($diff) }}
                                @else
                                    → Sama
                                @endif vs bulan lalu
                            </span>
                        </div>
                        <div id="chartBulanan"></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Jenis Pelanggaran Terbanyak</div>
                        <div class="chart-subtitle">Berdasarkan frekuensi kejadian</div>
                        <div id="chartJenis"></div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
             GURU BK & WALI KELAS
        ══════════════════════════════════════════════════ --}}
        @else
            <div>
                <p class="section-label">Ringkasan Data</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#e8f0fe">🎓</div>
                        <div>
                            <div class="dash-card-label">Jumlah Siswa</div>
                            <div class="dash-card-value">{{ $stats['jumlah_siswa'] }}</div>
                            <div class="dash-card-sub">
                                {{ $role === 'wali_kelas' ? 'siswa di kelas Anda' : 'total seluruh siswa' }}
                            </div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#fce8e8">📋</div>
                        <div>
                            <div class="dash-card-label">Total Pelanggaran</div>
                            <div class="dash-card-value">{{ $stats['total_pelanggaran'] }}</div>
                            <div class="dash-card-sub">seluruh catatan pelanggaran</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#e6ffed">✅</div>
                        <div>
                            <div class="dash-card-label">Sudah Ditindak</div>
                            <div class="dash-card-value" style="color:#276749">{{ $stats['sudah_ditindak'] }}</div>
                            <div class="dash-card-sub">sudah mendapat pembinaan</div>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#fff5e6">⏳</div>
                        <div>
                            <div class="dash-card-label">Belum Ditindak</div>
                            <div class="dash-card-value" style="color:#c05621">{{ $stats['belum_ditindak'] }}</div>
                            <div class="dash-card-sub">menunggu tindakan</div>
                        </div>
                    </div>

                </div>
            </div>

            <div>
                <p class="section-label">Grafik & Analisis</p>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div class="chart-card">
                        <div class="flex items-start justify-between flex-wrap gap-2">
                            <div>
                                <div class="chart-title">Tren Pelanggaran Per Bulan</div>
                                <div class="chart-subtitle">12 bulan terakhir</div>
                            </div>
                            @php
                                $d = $charts['bulanan']['data'];
                                $last = end($d);
                                $prev = count($d) >= 2 ? $d[count($d) - 2] : 0;
                                $diff = $last - $prev;
                            @endphp
                            <span
                                class="trend-badge {{ $diff > 0 ? 'trend-up' : ($diff < 0 ? 'trend-down' : 'trend-same') }}">
                                @if ($diff > 0)
                                    ▲ Naik {{ $diff }}
                                @elseif ($diff < 0)
                                    ▼ Turun {{ abs($diff) }}
                                @else
                                    → Sama
                                @endif vs bulan lalu
                            </span>
                        </div>
                        <div id="chartBulanan"></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Jenis Pelanggaran Terbanyak</div>
                        <div class="chart-subtitle">Berdasarkan frekuensi kejadian</div>
                        <div id="chartJenis"></div>
                    </div>
                </div>
            </div>

        @endif

    </div>

    {{-- ══════════════════════════════════════════════════
         SCRIPTS
    ══════════════════════════════════════════════════ --}}
    <script>
        // ── Toggle kelengkapan card ──
        function toggleKelengkapan(i) {
            const card = document.getElementById('card-' + i);
            card.classList.toggle('open');
        }
    
        // ── ApexCharts globals ──
        const navyColor = '#0D2D6B';
        const goldColor = '#F5B800';
        const gridColor = '#f0f4fb';
    
        const baseOptions = {
            chart:      { toolbar: { show: false }, fontFamily: 'inherit' },
            grid:       { borderColor: gridColor, strokeDashArray: 4 },
            tooltip:    { theme: 'light' },
            dataLabels: { enabled: false },
        };
    
        let chartInstances = [];
    
        function destroyCharts() {
            chartInstances.forEach(c => { try { c.destroy(); } catch (e) {} });
            chartInstances = [];
        }
    
        function initCharts() {
            destroyCharts();
    
            @if ($role === 'admin')
    
                // ── Chart 1: Pertumbuhan Pengguna Per Bulan ──
                const penggunaData = @json($charts['pengguna_bulanan']['data']);
                const maxPengguna  = Math.max(1, Math.max(...penggunaData));
    
                const c1 = new ApexCharts(document.querySelector('#chartPenggunaBulanan'), {
                    ...baseOptions,
                    series: [{ name: 'Pengguna Baru', data: penggunaData }],
                    chart:  { ...baseOptions.chart, type: 'bar', height: 260 },
                    colors: [navyColor],
                    plotOptions: {
                        bar: { borderRadius: 6, columnWidth: '50%', dataLabels: { position: 'top' } },
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -18,
                        style: { fontSize: '11px', fontWeight: 700, colors: [navyColor] },
                        formatter: v => v > 0 ? v : '',
                    },
                    xaxis: {
                        categories: @json($charts['pengguna_bulanan']['labels']),
                        labels: { style: { fontSize: '11px', colors: '#718096' } },
                        axisBorder: { show: false },
                        axisTicks:  { show: false },
                    },
                    yaxis: {
                        min: 0,
                        tickAmount: maxPengguna,
                        labels: {
                            style: { fontSize: '11px', colors: '#718096' },
                            formatter: v => Number.isInteger(v) ? v : '',
                        },
                    },
                    tooltip: { theme: 'light', y: { formatter: v => v + ' pengguna' } },
                });
                c1.render();
                chartInstances.push(c1);
    
                // ── Chart 2: Distribusi Siswa per Kelas ──
                const c2 = new ApexCharts(document.querySelector('#chartSiswaKelas'), {
                    ...baseOptions,
                    series: [{ name: 'Jumlah Siswa', data: @json($charts['siswa_per_kelas']['data']) }],
                    chart:  { ...baseOptions.chart, type: 'bar', height: 260 },
                    colors: [goldColor],
                    plotOptions: {
                        bar: { borderRadius: 6, horizontal: true, barHeight: '55%' },
                    },
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '11px', fontWeight: 700, colors: [navyColor] },
                        formatter: v => v > 0 ? v + ' siswa' : '',
                    },
                    xaxis: {
                        categories: @json($charts['siswa_per_kelas']['labels']),
                        labels: { style: { fontSize: '11px', colors: '#718096' } },
                        axisBorder: { show: false },
                        axisTicks:  { show: false },
                    },
                    yaxis: {
                        labels: { style: { fontSize: '11px', colors: '#718096' } },
                    },
                    tooltip: { theme: 'light', y: { formatter: v => v + ' siswa' } },
                });
                c2.render();
                chartInstances.push(c2);
    
            @else
    
                // ── Chart 3: Tren Pelanggaran Per Bulan ──
                const bulananData = @json($charts['bulanan']['data']);
                const maxBulanan  = Math.max(1, Math.max(...bulananData));
    
                const c3 = new ApexCharts(document.querySelector('#chartBulanan'), {
                    ...baseOptions,
                    series: [{ name: 'Pelanggaran', data: bulananData }],
                    chart:  { ...baseOptions.chart, type: 'area', height: 280 },
                    colors: [navyColor],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo:   0.02,
                            stops: [0, 90, 100],
                        },
                    },
                    stroke:  { curve: 'smooth', width: 2.5 },
                    markers: {
                        size: 5,
                        colors: ['#fff'],
                        strokeColors: [navyColor],
                        strokeWidth: 2.5,
                        hover: { size: 7 },
                    },
                    xaxis: {
                        categories: @json($charts['bulanan']['labels']),
                        labels: {
                            style: { fontSize: '10px', colors: '#718096' },
                            rotate: -45,
                            rotateAlways: false,
                            hideOverlappingLabels: true,
                        },
                        axisBorder: { show: false },
                        axisTicks:  { show: false },
                    },
                    yaxis: {
                        min: 0,
                        tickAmount: maxBulanan,
                        labels: {
                            style: { fontSize: '11px', colors: '#718096' },
                            formatter: v => Number.isInteger(v) ? v : '',
                        },
                    },
                    tooltip: {
                        theme: 'light',
                        x: { show: true },
                        y: { formatter: v => v + ' pelanggaran' },
                    },
                });
                c3.render();
                chartInstances.push(c3);
    
                // ── Chart 4: Jenis Pelanggaran Terbanyak ──
                @php
                    $tingkatList = $charts['jenis']['tingkat'] ?? [];
                    $jenisList   = $charts['jenis']['labels']  ?? [];
                    $jenisData   = $charts['jenis']['data']    ?? [];
                @endphp
    
                const jenisLabels  = @json($jenisList);
                const jenisTingkat = @json($tingkatList);
                const jenisData    = @json($jenisData);
                const maxJenis     = Math.max(1, Math.max(...jenisData));
    
                // ← lowercase agar cocok meski DB simpan huruf kecil/kapital
                const tingkatColors = jenisTingkat.map(t => {
                    const val = t ? t.toLowerCase() : '';
                    if (val === 'berat')  return '#ef4444'; // merah
                    if (val === 'sedang') return '#f97316'; // oranye
                    return '#F5B800';                       // kuning (ringan/default)
                });
    
                const c4 = new ApexCharts(document.querySelector('#chartJenis'), {
                    ...baseOptions,
                    series: [{ name: 'Jumlah Kejadian', data: jenisData }],
                    chart:  { ...baseOptions.chart, type: 'bar', height: Math.max(200, jenisLabels.length * 70) },
                    colors: [function({ dataPointIndex }) {
                        return tingkatColors[dataPointIndex] || '#F5B800';
                    }],
                    plotOptions: {
                        bar: {
                            borderRadius: 5,
                            horizontal:  true,   // ← nama terbaca penuh di kiri
                            barHeight:   '55%',
                            distributed: true,   // ← warna per-bar
                            dataLabels:  { position: 'right' },
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        offsetX: 6,
                        style: { fontSize: '11px', fontWeight: 700, colors: ['#1e3a6e'] },
                        formatter: v => v > 0 ? v + 'x' : '',
                    },
                    legend: { show: false },
                    // ← categories WAJIB di xaxis untuk horizontal bar
                    xaxis: {
                        categories: jenisLabels,
                        min: 0,
                        tickAmount: maxJenis,
                        labels: {
                            style: { fontSize: '11px', colors: '#718096' },
                            formatter: v => Number.isInteger(Number(v)) ? Math.floor(Number(v)) : '',
                        },
                        axisBorder: { show: false },
                        axisTicks:  { show: false },
                    },
                    yaxis: {
                        labels: {
                            maxWidth: 160,
                            style: { fontSize: '11px', colors: '#1e3a6e', fontWeight: 600 },
                            // potong nama panjang, tampilkan lengkap di tooltip
                            formatter: v => v && v.length > 24 ? v.substring(0, 24) + '…' : v,
                        },
                    },
                    tooltip: {
                        theme: 'light',
                        x: {
                            // nama lengkap + tingkat tampil di tooltip
                            formatter: (val, { dataPointIndex }) =>
                                `<strong>${jenisLabels[dataPointIndex] ?? ''}</strong>` +
                                `<br>Tingkat: ${jenisTingkat[dataPointIndex] ?? '-'}`,
                        },
                        y: {
                            title:     { formatter: () => '' },
                            formatter: v => v + ' kejadian',
                        },
                    },
                });
                c4.render();
                chartInstances.push(c4);
    
            @endif
        }
    
        document.addEventListener('DOMContentLoaded', initCharts);
        document.addEventListener('livewire:navigated', initCharts);
    </script>

</x-app-layout>
