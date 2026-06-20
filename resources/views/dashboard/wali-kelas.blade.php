<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-semibold text-[#0D2D6B]">
            Dashboard
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        .dash-card {
            background: #fff; border-radius: 14px; padding: 1.1rem 1.25rem;
            box-shadow: 0 2px 12px rgba(13,45,107,0.08);
            border: 1px solid rgba(13,45,107,0.07);
            display: flex; align-items: center; gap: 0.9rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .dash-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(13,45,107,0.13); }
        .dash-card-icon  { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .dash-card-label { font-size: 0.68rem; font-weight: 600; color: #4A5E8A; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.15rem; }
        .dash-card-value { font-size: 1.75rem; font-weight: 800; color: #0D2D6B; line-height: 1; }
        .dash-card-sub   { font-size: 0.68rem; color: #718096; margin-top: 0.2rem; }
        .chart-card      { background: #fff; border-radius: 14px; padding: 1.25rem 1.25rem 1rem; box-shadow: 0 2px 12px rgba(13,45,107,0.08); border: 1px solid rgba(13,45,107,0.07); }
        .chart-title     { font-size: 0.88rem; font-weight: 700; color: #0D2D6B; margin-bottom: 0.15rem; }
        .chart-subtitle  { font-size: 0.7rem; color: #718096; margin-bottom: 0.75rem; }
        .section-label   { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #4A5E8A; margin-bottom: 0.65rem; }
        .trend-badge     { display: inline-flex; align-items: center; gap: 0.2rem; font-size: 0.68rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 20px; }
        .trend-up   { background: #ffe4e4; color: #c53030; }
        .trend-down { background: #e6ffed; color: #276749; }
        .trend-same { background: #f0f4fb; color: #4A5E8A; }

        /* ── Scroll chart bulanan di mobile ── */
        .chart-scroll-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 transparent;
            border-radius: 8px;
        }
        .chart-scroll-wrapper::-webkit-scrollbar { height: 4px; }
        .chart-scroll-wrapper::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }
        .chart-inner-bulanan { min-width: 640px; }
        @media (min-width: 768px) {
            .chart-scroll-wrapper { overflow-x: visible; }
            .chart-inner-bulanan  { min-width: unset; }
        }

        .swipe-hint { display: none; font-size: 0.65rem; color: #a0aec0; text-align: center; margin-top: 0.4rem; }
        @media (max-width: 767px) { .swipe-hint { display: block; } }
    </style>

    <div class="space-y-5">

        {{-- GREETING --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#4A5E8A]">Selamat datang kembali,</p>
                <h3 class="text-xl font-bold text-[#0D2D6B]">{{ auth()->user()->name }} 👋</h3>
                @if ($kelas)
                    <div class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                bg-[#e8f0fe] text-[#0D2D6B] text-xs font-semibold">
                        🏫 Wali Kelas {{ $kelas->nama_kelas }}
                    </div>
                @endif
            </div>
            <span class="text-xs text-[#718096] hidden sm:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>

        {{-- STATS --}}
        <div>
            <p class="section-label">Ringkasan Data</p>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">

                <div class="dash-card">
                    <div class="dash-card-icon" style="background:#e8f0fe">🎓</div>
                    <div>
                        <div class="dash-card-label">Jumlah Siswa</div>
                        <div class="dash-card-value">{{ $stats['jumlah_siswa'] }}</div>
                        <div class="dash-card-sub">siswa di kelas Anda</div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card-icon" style="background:#fce8e8">📋</div>
                    <div>
                        <div class="dash-card-label">Total Pelanggaran</div>
                        <div class="dash-card-value">{{ $stats['total_pelanggaran'] }}</div>
                        <div class="dash-card-sub">seluruh catatan</div>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card-icon" style="background:#e6ffed">✅</div>
                    <div>
                        <div class="dash-card-label">Sudah Ditindak</div>
                        <div class="dash-card-value" style="color:#276749">{{ $stats['sudah_ditindak'] }}</div>
                        <div class="dash-card-sub">sudah dibina</div>
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

        {{-- CHARTS --}}
        <div>
            <p class="section-label">Grafik &amp; Analisis</p>
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

                <div class="chart-card">
                    <div class="flex items-start justify-between flex-wrap gap-2 mb-1">
                        <div>
                            <div class="chart-title">Tren Pelanggaran Per Bulan</div>
                            <div class="chart-subtitle">12 bulan terakhir</div>
                        </div>
                        @php
                            $d    = $charts['bulanan']['data'] ?? [];
                            $last = !empty($d) ? end($d) : 0;
                            $prev = count($d) >= 2 ? $d[count($d)-2] : 0;
                            $diff = $last - $prev;
                        @endphp
                        <span class="trend-badge {{ $diff > 0 ? 'trend-up' : ($diff < 0 ? 'trend-down' : 'trend-same') }}">
                            @if ($diff > 0) ▲ Naik {{ $diff }}
                            @elseif ($diff < 0) ▼ Turun {{ abs($diff) }}
                            @else → Sama
                            @endif vs bulan lalu
                        </span>
                    </div>
                    <div class="chart-scroll-wrapper">
                        <div class="chart-inner-bulanan">
                            <div id="chartBulanan"></div>
                        </div>
                    </div>
                    <p class="swipe-hint">← geser untuk melihat data →</p>
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
    (function () {
        const CHART_KEY = '_charts_dashboard_walikelas';
        const CHART_IDS = ['chartBulanan', 'chartJenis'];

        const _bulananData   = @json($charts['bulanan']['data']   ?? []);
        const _bulananLabels = @json($charts['bulanan']['labels'] ?? []);
        const _jenisLabels   = @json($charts['jenis']['labels']   ?? []);
        const _jenisTingkat  = @json($charts['jenis']['tingkat']  ?? []);
        const _jenisData     = @json($charts['jenis']['data']     ?? []);

        const navyColor = '#0D2D6B';
        const gridColor = '#f0f4fb';
        const isMobile  = () => window.innerWidth < 768;

        const baseOpts = {
            chart:      { toolbar: { show: false }, fontFamily: 'inherit' },
            grid:       { borderColor: gridColor, strokeDashArray: 4 },
            tooltip:    { theme: 'light' },
            dataLabels: { enabled: false },
        };

        let _retryCount = 0;
        const MAX_RETRY = 20;
        if (!window[CHART_KEY]) window[CHART_KEY] = [];

        function destroyCharts() {
            (window[CHART_KEY] || []).forEach(c => { try { c.destroy(); } catch (e) {} });
            window[CHART_KEY] = [];
            CHART_IDS.forEach(id => { const el = document.getElementById(id); if (el) el.innerHTML = ''; });
        }

        function initCharts() {
            const bulananEl = document.getElementById('chartBulanan');
            const jenisEl   = document.getElementById('chartJenis');
            if (!bulananEl || !jenisEl) {
                if (_retryCount < MAX_RETRY) { _retryCount++; setTimeout(initCharts, 100); }
                return;
            }
            _retryCount = 0;
            destroyCharts();

            const mobile = isMobile();
            const maxBulanan = Math.max(1, ...(_bulananData.length ? _bulananData : [1]));

            const c1 = new ApexCharts(bulananEl, {
                ...baseOpts,
                series: [{ name: 'Pelanggaran', data: _bulananData }],
                chart: {
                    ...baseOpts.chart, type: 'area', height: 260,
                    zoom: { enabled: false }, pan: { enabled: false }, selection: { enabled: false },
                },
                colors: [navyColor],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.42, opacityTo: 0.02, stops: [0, 90, 100] } },
                stroke:  { curve: 'smooth', width: 2.5 },
                markers: { size: mobile ? 4 : 5, colors: ['#fff'], strokeColors: [navyColor], strokeWidth: 2.5, hover: { size: mobile ? 6 : 7 } },
                xaxis: {
                    categories: _bulananLabels,
                    labels: { style: { fontSize: mobile ? '9px' : '10px', colors: '#718096' }, rotate: -40, hideOverlappingLabels: false },
                    axisBorder: { show: false }, axisTicks: { show: false },
                },
                yaxis: {
                    min: 0, tickAmount: maxBulanan,
                    labels: { style: { fontSize: '11px', colors: '#718096' }, formatter: v => Number.isInteger(v) ? v : '' },
                },
                tooltip: { theme: 'light', shared: true, intersect: false, y: { formatter: v => v + ' pelanggaran' } },
            });
            c1.render();
            window[CHART_KEY].push(c1);

            const maxJenis = Math.max(1, ...(_jenisData.length ? _jenisData : [1]));
            const tingkatColors = _jenisTingkat.map(t => {
                const val = t ? t.toLowerCase() : '';
                if (val === 'berat')  return '#ef4444';
                if (val === 'sedang') return '#f97316';
                return '#F5B800';
            });

            const c2 = new ApexCharts(jenisEl, {
                ...baseOpts,
                series: [{ name: 'Jumlah Kejadian', data: _jenisData }],
                chart: {
                    ...baseOpts.chart, type: 'bar',
                    height: Math.max(180, _jenisLabels.length * (mobile ? 56 : 70)),
                    zoom: { enabled: false }, selection: { enabled: false },
                },
                colors: [function ({ dataPointIndex }) { return tingkatColors[dataPointIndex] || '#F5B800'; }],
                plotOptions: { bar: { borderRadius: 5, horizontal: true, distributed: true, barHeight: mobile ? '60%' : '55%' } },
                dataLabels: { enabled: true, offsetX: 6, style: { fontSize: '11px', fontWeight: 700, colors: ['#1e3a6e'] }, formatter: v => v > 0 ? v + 'x' : '' },
                legend: { show: false },
                xaxis: {
                    categories: _jenisLabels, min: 0, tickAmount: maxJenis,
                    labels: { style: { fontSize: '11px', colors: '#718096' }, formatter: v => Number.isInteger(Number(v)) ? Math.floor(Number(v)) : '' },
                    axisBorder: { show: false }, axisTicks: { show: false },
                },
                yaxis: {
                    labels: {
                        maxWidth: mobile ? 110 : 160,
                        style: { fontSize: mobile ? '10px' : '11px', colors: '#1e3a6e', fontWeight: 600 },
                        formatter: v => v && v.length > (mobile ? 16 : 24) ? v.substring(0, mobile ? 16 : 24) + '…' : v,
                    },
                },
                tooltip: {
                    theme: 'light', shared: false, intersect: true,
                    x: { formatter: (val, { dataPointIndex }) => `<strong>${_jenisLabels[dataPointIndex] ?? ''}</strong><br>Tingkat: ${_jenisTingkat[dataPointIndex] ?? '-'}` },
                    y: { title: { formatter: () => '' }, formatter: v => v + ' kejadian' },
                },
            });
            c2.render();
            window[CHART_KEY].push(c2);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => setTimeout(initCharts, 80));
        } else {
            setTimeout(initCharts, 80);
        }

        document.addEventListener('livewire:navigate',  () => { destroyCharts(); _retryCount = 0; });
        document.addEventListener('livewire:navigated', () => { _retryCount = 0; setTimeout(initCharts, 80); });

        let _resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(_resizeTimer);
            _resizeTimer = setTimeout(() => { _retryCount = 0; initCharts(); }, 300);
        });
    })();
    </script>

</x-app-layout>
