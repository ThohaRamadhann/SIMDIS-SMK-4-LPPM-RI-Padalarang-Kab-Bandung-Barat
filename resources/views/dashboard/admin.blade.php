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
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
        }
        .dash-card-label {
            font-size: 0.72rem; font-weight: 600; color: #4A5E8A;
            text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem;
        }
        .dash-card-value { font-size: 1.9rem; font-weight: 800; color: #0D2D6B; line-height: 1; }
        .dash-card-sub   { font-size: 0.72rem; color: #718096; margin-top: 0.25rem; }
        .chart-card {
            background: #fff; border-radius: 14px; padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
        }
        .chart-title    { font-size: 0.9rem; font-weight: 700; color: #0D2D6B; margin-bottom: 0.2rem; }
        .chart-subtitle { font-size: 0.72rem; color: #718096; margin-bottom: 1rem; }
        .section-label  {
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: #4A5E8A; margin-bottom: 0.75rem;
        }
        .kelengkapan-card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
            overflow: hidden; transition: box-shadow 0.18s ease;
        }
        .kelengkapan-card:hover { box-shadow: 0 6px 20px rgba(13, 45, 107, 0.13); }
        .kelengkapan-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.9rem 1.1rem; cursor: pointer; user-select: none; gap: 0.75rem;
        }
        .kelengkapan-header-left { display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0; }
        .kelengkapan-icon-wrap {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .kelengkapan-judul  { font-size: 0.83rem; font-weight: 700; color: #1a202c; line-height: 1.3; }
        .kelengkapan-sub    { font-size: 0.7rem; color: #718096; margin-top: 0.1rem; }
        .kelengkapan-badge  { font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px; flex-shrink: 0; }
        .kelengkapan-chevron { flex-shrink: 0; transition: transform 0.2s ease; color: #a0aec0; font-size: 0.75rem; }
        .kelengkapan-card.open .kelengkapan-chevron { transform: rotate(180deg); }
        .kelengkapan-body { display: none; border-top: 1px solid #f0f4fb; padding: 0.75rem 1.1rem 1rem; }
        .kelengkapan-card.open .kelengkapan-body { display: block; }
        .kelengkapan-item {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.35rem 0; font-size: 0.8rem; color: #2d3748;
            border-bottom: 1px dashed #f0f4fb;
        }
        .kelengkapan-item:last-child { border-bottom: none; }
        .kelengkapan-item::before   { content: '•'; color: #a0aec0; flex-shrink: 0; }
        .kelengkapan-more  { font-size: 0.72rem; color: #718096; margin-top: 0.5rem; font-style: italic; }
        .kelengkapan-link  {
            display: inline-flex; align-items: center; gap: 0.3rem;
            margin-top: 0.75rem; font-size: 0.75rem; font-weight: 600; color: #0D2D6B;
            text-decoration: none; padding: 0.3rem 0.75rem;
            border: 1px solid rgba(13, 45, 107, 0.2); border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }
        .kelengkapan-link:hover { background: #0D2D6B; color: #fff; }
        .tipe-warning .kelengkapan-icon-wrap  { background: #fff8e1; }
        .tipe-warning .kelengkapan-badge      { background: #fff8e1; color: #b7791f; }
        .tipe-warning .kelengkapan-header     { border-left: 4px solid #F5B800; }
        .tipe-danger  .kelengkapan-icon-wrap  { background: #fff0f0; }
        .tipe-danger  .kelengkapan-badge      { background: #fff0f0; color: #c53030; }
        .tipe-danger  .kelengkapan-header     { border-left: 4px solid #fc8181; }
        .tipe-info    .kelengkapan-icon-wrap  { background: #ebf8ff; }
        .tipe-info    .kelengkapan-badge      { background: #ebf8ff; color: #2b6cb0; }
        .tipe-info    .kelengkapan-header     { border-left: 4px solid #63b3ed; }
        .kelengkapan-ok {
            background: #fff; border-radius: 14px; padding: 2rem; text-align: center;
            box-shadow: 0 2px 12px rgba(13, 45, 107, 0.08);
            border: 1px solid rgba(13, 45, 107, 0.07);
        }
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
                            <div class="kelengkapan-body">
                                @foreach ($item['detail'] as $baris)
                                    <div class="kelengkapan-item">{{ $baris }}</div>
                                @endforeach
                                @if ($item['ada_lagi'] > 0)
                                    <p class="kelengkapan-more">+ {{ $item['ada_lagi'] }} data lainnya tidak ditampilkan</p>
                                @endif
                                <a href="{{ $item['link'] }}" class="kelengkapan-link">Kelola Data →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="kelengkapan-ok">
                    <div style="font-size:2.5rem;margin-bottom:.5rem">✅</div>
                    <p style="font-weight:700;color:#276749;font-size:.9rem">Semua Data Sudah Lengkap</p>
                    <p style="font-size:.75rem;color:#718096;margin-top:.25rem">Tidak ada data yang bermasalah saat ini.</p>
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

    </div>

    <script>
        (function () {
        
            const CHART_KEY = '_charts_dashboard_admin';
            const CHART_IDS = ['chartPenggunaBulanan', 'chartSiswaKelas'];
        
            const _penggunaData  = @json($charts['pengguna_bulanan']['data']);
            const _penggunaLabel = @json($charts['pengguna_bulanan']['labels']);
            const _kelasData     = @json($charts['siswa_per_kelas']['data']);
            const _kelasLabel    = @json($charts['siswa_per_kelas']['labels']);
        
            const navyColor = '#0D2D6B';
            const goldColor = '#F5B800';
            const gridColor = '#f0f4fb';
            const baseOpts  = {
                chart:      { toolbar: { show: false }, fontFamily: 'inherit' },
                grid:       { borderColor: gridColor, strokeDashArray: 4 },
                tooltip:    { theme: 'light' },
                dataLabels: { enabled: false },
            };
        
            let _retryCount = 0;
            const MAX_RETRY = 30;
        
            if (!window[CHART_KEY]) window[CHART_KEY] = [];
        
            function destroyCharts() {
                (window[CHART_KEY] || []).forEach(c => { try { c.destroy(); } catch (e) {} });
                window[CHART_KEY] = [];
                CHART_IDS.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.innerHTML = '';
                });
            }
        
            function initCharts() {
                // 1. Pastikan library sudah load
                if (typeof ApexCharts === 'undefined') {
                    if (_retryCount < MAX_RETRY) { _retryCount++; setTimeout(initCharts, 100); }
                    return;
                }
        
                // 2. Pastikan kedua elemen DOM sudah ada
                const container1 = document.getElementById('chartPenggunaBulanan');
                const container2 = document.getElementById('chartSiswaKelas');
                if (!container1 || !container2) {
                    if (_retryCount < MAX_RETRY) { _retryCount++; setTimeout(initCharts, 100); }
                    return;
                }
        
                _retryCount = 0;
                destroyCharts();
        
                // ── Chart 1: Pertumbuhan Pengguna ──
                const maxPengguna = Math.max(1, ..._penggunaData);
                const c1 = new ApexCharts(document.getElementById('chartPenggunaBulanan'), {
                    ...baseOpts,
                    series: [{ name: 'Pengguna Baru', data: _penggunaData }],
                    chart:  { ...baseOpts.chart, type: 'bar', height: 260 },
                    colors: [navyColor],
                    plotOptions: {
                        bar: { borderRadius: 6, columnWidth: '50%', dataLabels: { position: 'top' } },
                    },
                    dataLabels: {
                        enabled: true, offsetY: -18,
                        style: { fontSize: '11px', fontWeight: 700, colors: [navyColor] },
                        formatter: v => v > 0 ? v : '',
                    },
                    xaxis: {
                        categories: _penggunaLabel,
                        labels: { style: { fontSize: '11px', colors: '#718096' } },
                        axisBorder: { show: false }, axisTicks: { show: false },
                    },
                    yaxis: {
                        min: 0, tickAmount: maxPengguna,
                        labels: {
                            style: { fontSize: '11px', colors: '#718096' },
                            formatter: v => Number.isInteger(v) ? v : '',
                        },
                    },
                    tooltip: { theme: 'light', y: { formatter: v => v + ' pengguna' } },
                });
                c1.render();
                window[CHART_KEY].push(c1);
        
                // ── Chart 2: Distribusi Siswa per Kelas ──
                const c2 = new ApexCharts(document.getElementById('chartSiswaKelas'), {
                    ...baseOpts,
                    series: [{ name: 'Jumlah Siswa', data: _kelasData }],
                    chart:  { ...baseOpts.chart, type: 'bar', height: 260 },
                    colors: [goldColor],
                    plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '55%' } },
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '11px', fontWeight: 700, colors: [navyColor] },
                        formatter: v => v > 0 ? v + ' siswa' : '',
                    },
                    xaxis: {
                        categories: _kelasLabel,
                        labels: { style: { fontSize: '11px', colors: '#718096' } },
                        axisBorder: { show: false }, axisTicks: { show: false },
                    },
                    yaxis: { labels: { style: { fontSize: '11px', colors: '#718096' } } },
                    tooltip: { theme: 'light', y: { formatter: v => v + ' siswa' } },
                });
                c2.render();
                window[CHART_KEY].push(c2);
            }
        
            window.toggleKelengkapan = function (i) {
                const card = document.getElementById('card-' + i);
                if (card) card.classList.toggle('open');
            };
        
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => setTimeout(initCharts, 150));
            } else {
                setTimeout(initCharts, 150);
            }
        
            document.addEventListener('livewire:navigate', function () {
                destroyCharts();
                _retryCount = 0;
            });
        
            document.addEventListener('livewire:navigated', function () {
                _retryCount = 0;
                setTimeout(initCharts, 200);
            });
        
        })();
        </script>

</x-app-layout>