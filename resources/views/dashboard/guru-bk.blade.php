<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold text-[#0D2D6B]">
            Dashboard
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        .dash-card {
            background: #fff; border-radius: 14px; padding: 1.35rem 1.5rem;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
            display: flex; align-items: center; gap: 1rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .dash-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(13, 45, 107, 0.13); }
        .dash-card-icon  { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .dash-card-label { font-size: 0.72rem; font-weight: 600; color: #4A5E8A; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
        .dash-card-value { font-size: 1.9rem; font-weight: 800; color: #0D2D6B; line-height: 1; }
        .dash-card-sub   { font-size: 0.72rem; color: #718096; margin-top: 0.25rem; }
        .chart-card      { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08); border: 1px solid rgba(13, 45, 107, 0.07); }
        .chart-title     { font-size: 0.9rem; font-weight: 700; color: #0D2D6B; margin-bottom: 0.2rem; }
        .chart-subtitle  { font-size: 0.72rem; color: #718096; margin-bottom: 1rem; }
        .section-label   { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #4A5E8A; margin-bottom: 0.75rem; }
        .trend-badge     { display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.72rem; font-weight: 600; padding: 0.2rem 0.55rem; border-radius: 20px; }
        .trend-up   { background: #ffe4e4; color: #c53030; }
        .trend-down { background: #e6ffed; color: #276749; }
        .trend-same { background: #f0f4fb; color: #4A5E8A; }
    </style>

    <div class="space-y-6">

        {{-- GREETING --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#4A5E8A]">Selamat datang kembali,</p>
                <h3 class="text-xl font-bold text-[#0D2D6B]">{{ auth()->user()->name }} 👋</h3>
            </div>
            <span class="text-xs text-[#718096] hidden sm:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        {{-- Stats Cards --}}
        <div>
            <p class="section-label">Ringkasan Data</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="dash-card">
                    <div class="dash-card-icon" style="background:#e8f0fe">🎓</div>
                    <div>
                        <div class="dash-card-label">Jumlah Siswa</div>
                        <div class="dash-card-value">{{ $stats['jumlah_siswa'] }}</div>
                        <div class="dash-card-sub">total seluruh siswa</div>
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

        {{-- Charts --}}
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
                            $d    = $charts['bulanan']['data'];
                            $last = end($d);
                            $prev = count($d) >= 2 ? $d[count($d) - 2] : 0;
                            $diff = $last - $prev;
                        @endphp
                        <span class="trend-badge {{ $diff > 0 ? 'trend-up' : ($diff < 0 ? 'trend-down' : 'trend-same') }}">
                            @if ($diff > 0) ▲ Naik {{ $diff }}
                            @elseif ($diff < 0) ▼ Turun {{ abs($diff) }}
                            @else → Sama
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

    </div>

    <script>
        const navyColor = '#0D2D6B';
        const goldColor = '#F5B800';
        const gridColor = '#f0f4fb';
    
        const baseOptions = {
            chart: {
                toolbar: {
                    show: false
                },
                fontFamily: 'inherit'
            },
    
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4
            },
    
            tooltip: {
                theme: 'light'
            },
    
            dataLabels: {
                enabled: false
            },
        };
    
        // =========================================================
        // GLOBAL STORAGE
        // =========================================================
        window._simdisCharts = window._simdisCharts || [];
    
        let chartsInitialized = false;
    
        // =========================================================
        // DESTROY CHARTS
        // =========================================================
        function destroyCharts() {
    
            window._simdisCharts.forEach(chart => {
    
                try {
                    chart.destroy();
                } catch (e) {}
    
            });
    
            window._simdisCharts = [];
    
            ['chartBulanan', 'chartJenis'].forEach(id => {
    
                const el = document.getElementById(id);
    
                if (el) {
                    el.innerHTML = '';
                    el.replaceChildren();
                }
            });
        }
    
        // =========================================================
        // INIT CHARTS
        // =========================================================
        function initCharts() {
    
            const bulananEl = document.getElementById('chartBulanan');
            const jenisEl = document.getElementById('chartJenis');
    
            if (!bulananEl || !jenisEl) {
                return;
            }
    
            setTimeout(() => {
    
                // =====================================================
                // DATA BULANAN
                // =====================================================
    
                const bulananData = @json($charts['bulanan']['data'] ?? []);
                const bulananLabels = @json($charts['bulanan']['labels'] ?? []);
    
                const maxBulanan = bulananData.length ?
                    Math.max(...bulananData) :
                    1;
    
                const c1 = new ApexCharts(bulananEl, {
    
                    ...baseOptions,
    
                    series: [{
                        name: 'Pelanggaran',
                        data: bulananData
                    }],
    
                    chart: {
                        ...baseOptions.chart,
                        type: 'area',
                        height: 280
                    },
    
                    colors: [navyColor],
    
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.02,
                            stops: [0, 90, 100]
                        }
                    },
    
                    stroke: {
                        curve: 'smooth',
                        width: 2.5
                    },
    
                    markers: {
                        size: 5,
                        colors: ['#fff'],
                        strokeColors: [navyColor],
                        strokeWidth: 2.5,
                        hover: {
                            size: 7
                        }
                    },
    
                    xaxis: {
                        categories: bulananLabels,
    
                        labels: {
                            style: {
                                fontSize: '10px',
                                colors: '#718096'
                            },
    
                            rotate: -45,
                            hideOverlappingLabels: true
                        },
    
                        axisBorder: {
                            show: false
                        },
    
                        axisTicks: {
                            show: false
                        },
                    },
    
                    yaxis: {
                        min: 0,
                        tickAmount: maxBulanan,
    
                        labels: {
                            style: {
                                fontSize: '11px',
                                colors: '#718096'
                            },
    
                            formatter: v =>
                                Number.isInteger(v) ? v : ''
                        }
                    },
    
                    tooltip: {
                        theme: 'light',
    
                        y: {
                            formatter: v => v + ' pelanggaran'
                        }
                    }
                });
    
                c1.render();
    
                window._simdisCharts.push(c1);
    
                // =====================================================
                // DATA JENIS
                // =====================================================
    
                const jenisLabels = @json($charts['jenis']['labels'] ?? []);
                const jenisTingkat = @json($charts['jenis']['tingkat'] ?? []);
                const jenisData = @json($charts['jenis']['data'] ?? []);
    
                const maxJenis = jenisData.length ?
                    Math.max(...jenisData) :
                    1;
    
                const tingkatColors = jenisTingkat.map(t => {
    
                    const val = t ? t.toLowerCase() : '';
    
                    if (val === 'berat') {
                        return '#ef4444';
                    }
    
                    if (val === 'sedang') {
                        return '#f97316';
                    }
    
                    return '#F5B800';
                });
    
                const c2 = new ApexCharts(jenisEl, {
    
                    ...baseOptions,
    
                    series: [{
                        name: 'Jumlah Kejadian',
                        data: jenisData
                    }],
    
                    chart: {
                        ...baseOptions.chart,
                        type: 'bar',
                        height: Math.max(200, jenisLabels.length * 70)
                    },
    
                    colors: [
                        function({
                            dataPointIndex
                        }) {
                            return tingkatColors[dataPointIndex] || '#F5B800';
                        }
                    ],
    
                    plotOptions: {
                        bar: {
                            borderRadius: 5,
                            horizontal: true,
                            distributed: true,
                            barHeight: '55%',
                        }
                    },
    
                    dataLabels: {
                        enabled: true,
                        offsetX: 6,
    
                        style: {
                            fontSize: '11px',
                            fontWeight: 700,
                            colors: ['#1e3a6e']
                        },
    
                        formatter: v => v > 0 ? v + 'x' : ''
                    },
    
                    legend: {
                        show: false
                    },
    
                    xaxis: {
                        categories: jenisLabels,
                        min: 0,
                        tickAmount: maxJenis,
    
                        labels: {
                            style: {
                                fontSize: '11px',
                                colors: '#718096'
                            },
    
                            formatter: v =>
                                Number.isInteger(Number(v)) ?
                                Math.floor(Number(v)) :
                                ''
                        },
    
                        axisBorder: {
                            show: false
                        },
    
                        axisTicks: {
                            show: false
                        },
                    },
    
                    yaxis: {
                        labels: {
                            maxWidth: 160,
    
                            style: {
                                fontSize: '11px',
                                colors: '#1e3a6e',
                                fontWeight: 600
                            },
    
                            formatter: v =>
                                v && v.length > 24 ?
                                v.substring(0, 24) + '…' :
                                v
                        }
                    },
    
                    tooltip: {
                        theme: 'light',
    
                        x: {
                            formatter: (val, {
                                    dataPointIndex
                                }) =>
                                `<strong>${jenisLabels[dataPointIndex] ?? ''}</strong><br>Tingkat: ${jenisTingkat[dataPointIndex] ?? '-'}`
                        },
    
                        y: {
                            title: {
                                formatter: () => ''
                            },
    
                            formatter: v => v + ' kejadian'
                        }
                    }
                });
    
                c2.render();
    
                window._simdisCharts.push(c2);
    
            }, 120);
        }
    
        // =========================================================
        // SAFE INIT
        // =========================================================
        function safeInitCharts() {
    
            if (chartsInitialized) {
                return;
            }
    
            chartsInitialized = true;
    
            initCharts();
        }
    
        // =========================================================
        // SAFE DESTROY
        // =========================================================
        function safeDestroyCharts() {
    
            destroyCharts();
    
            chartsInitialized = false;
        }
    
        // =========================================================
        // FIRST LOAD
        // =========================================================
        window.addEventListener('load', () => {
            safeInitCharts();
        });
    
        // =========================================================
        // LIVEWIRE NAVIGATION
        // =========================================================
        document.addEventListener('livewire:navigated', () => {
            safeInitCharts();
        });
    
        // =========================================================
        // BEFORE NAVIGATE
        // =========================================================
        document.addEventListener('livewire:navigate', () => {
            safeDestroyCharts();
        });
    </script>

</x-app-layout>