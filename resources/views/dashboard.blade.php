<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold text-[#0D2D6B]">
            Dashboard
        </h2>
    </x-slot>

    {{-- ApexCharts CDN --}}
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
            font-size: 0.75rem;
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
            margin-bottom: 0.25rem;
        }

        .chart-subtitle {
            font-size: 0.75rem;
            color: #718096;
            margin-bottom: 1rem;
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

        .trend-up   { background: #ffe4e4; color: #c53030; }
        .trend-down { background: #e6ffed; color: #276749; }
        .trend-same { background: #f0f4fb; color: #4A5E8A; }

        .section-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #4A5E8A;
            margin-bottom: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>

    <div class="space-y-6">

        {{-- ── GREETING ──────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#4A5E8A]">Selamat datang kembali,</p>
                <h3 class="text-xl font-bold text-[#0D2D6B]">{{ auth()->user()->name }} 👋</h3>
            </div>
            <span class="text-xs text-[#718096] hidden sm:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        {{-- ── STAT CARDS ─────────────────────────────────────────────── --}}
        <div>
            <p class="section-label">Ringkasan Data</p>

            @if ($role === 'orang_tua')
                {{-- Orang tua: 3 card --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div class="dash-card">
                        <div class="dash-card-icon" style="background:#e8f0fe">📋</div>
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

            @else
                {{-- guru_bk & wali_kelas: 4 card --}}
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
            @endif
        </div>

        {{-- ── CHARTS ──────────────────────────────────────────────────── --}}
        <div>
            <p class="section-label">Grafik & Analisis</p>
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

                {{-- Chart 1: Tren Pelanggaran Per Bulan --}}
                <div class="chart-card">
                    <div class="flex items-start justify-between flex-wrap gap-2">
                        <div>
                            <div class="chart-title">Tren Pelanggaran Per Bulan</div>
                            <div class="chart-subtitle">12 bulan terakhir</div>
                        </div>
                        @php
                            $dataChart  = $charts['bulanan']['data'];
                            $lastMonth  = end($dataChart);
                            $prevMonth  = count($dataChart) >= 2 ? $dataChart[count($dataChart) - 2] : 0;
                            $diff       = $lastMonth - $prevMonth;
                        @endphp
                        <span class="trend-badge {{ $diff > 0 ? 'trend-up' : ($diff < 0 ? 'trend-down' : 'trend-same') }}">
                            @if ($diff > 0) ▲ Naik {{ $diff }}
                            @elseif ($diff < 0) ▼ Turun {{ abs($diff) }}
                            @else → Sama
                            @endif
                            vs bulan lalu
                        </span>
                    </div>
                    <div id="chartBulanan"></div>
                </div>

                {{-- Chart 2: Jenis Pelanggaran Terbanyak --}}
                <div class="chart-card">
                    <div class="chart-title">Jenis Pelanggaran Terbanyak</div>
                    <div class="chart-subtitle">Berdasarkan frekuensi kejadian</div>
                    <div id="chartJenis"></div>
                </div>

            </div>
        </div>

    </div>

    {{-- ── APEXCHARTS SCRIPTS ───────────────────────────────────────── --}}
    <script>
        // Data dari Laravel
        const dataBulanan = @json($charts['bulanan']['data']);
        const labelBulanan = @json($charts['bulanan']['labels']);
        const dataJenis = @json($charts['jenis']['data']);
        const labelJenis = @json($charts['jenis']['labels']);

        // ── Chart 1: Line chart tren bulanan ──
        new ApexCharts(document.querySelector('#chartBulanan'), {
            series: [{
                name: 'Pelanggaran',
                data: dataBulanan,
            }],
            chart: {
                type: 'area',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'inherit',
                sparkline: { enabled: false },
            },
            colors: ['#0D2D6B'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.02,
                    stops: [0, 100],
                },
            },
            stroke: {
                curve: 'smooth',
                width: 2.5,
            },
            markers: {
                size: 4,
                colors: ['#fff'],
                strokeColors: ['#0D2D6B'],
                strokeWidth: 2,
            },
            xaxis: {
                categories: labelBulanan,
                labels: {
                    style: { fontSize: '11px', colors: '#718096' },
                    rotate: -30,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px', colors: '#718096' },
                    formatter: val => Math.floor(val),
                },
                min: 0,
            },
            grid: {
                borderColor: '#f0f4fb',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'light',
                y: { formatter: val => val + ' pelanggaran' },
            },
            dataLabels: { enabled: false },
        }).render();

        // ── Chart 2: Bar chart jenis pelanggaran ──
        new ApexCharts(document.querySelector('#chartJenis'), {
            series: [{
                name: 'Jumlah',
                data: dataJenis,
            }],
            chart: {
                type: 'bar',
                height: 260,
                toolbar: { show: false },
                fontFamily: 'inherit',
            },
            colors: ['#F5B800'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: false,
                    columnWidth: '50%',
                    dataLabels: { position: 'top' },
                },
            },
            dataLabels: {
                enabled: true,
                offsetY: -18,
                style: {
                    fontSize: '11px',
                    fontWeight: 700,
                    colors: ['#0D2D6B'],
                },
                formatter: val => val > 0 ? val : '',
            },
            xaxis: {
                categories: labelJenis,
                labels: {
                    style: { fontSize: '11px', colors: '#718096' },
                    trim: true,
                    maxHeight: 60,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px', colors: '#718096' },
                    formatter: val => Math.floor(val),
                },
                min: 0,
            },
            grid: {
                borderColor: '#f0f4fb',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'light',
                y: { formatter: val => val + ' kali' },
            },
        }).render();
    </script>

</x-app-layout>