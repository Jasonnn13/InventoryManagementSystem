<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-3">
                <h2 class="app-page-title">Beranda</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('pembelian.index', ['month' => date('m'), 'year' => date('Y')]) }}" class="app-card group flex flex-col justify-between transition duration-150 hover:-translate-y-0.5 hover:shadow-xl">
                <div class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">Pembelian bulan ini</p>
                    <div class="flex items-end justify-between gap-4">
                        <span class="text-4xl font-semibold tracking-tight">{{ $pembelianCount }}</span>
                        <span class="app-badge">Incoming goods</span>
                    </div>
                </div>
                <span class="mt-6 inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white dark:border-white dark:bg-white dark:text-black">Buka pembelian</span>
            </a>

            <a href="{{ route('penjualan.index', ['month' => date('m'), 'year' => date('Y')]) }}" class="app-card group flex flex-col justify-between transition duration-150 hover:-translate-y-0.5 hover:shadow-xl">
                <div class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">Penjualan bulan ini</p>
                    <div class="flex items-end justify-between gap-4">
                        <span class="text-4xl font-semibold tracking-tight">{{ $penjualanCount }}</span>
                        <span class="app-badge">Outgoing goods</span>
                    </div>
                </div>
                <span class="mt-6 inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white dark:border-white dark:bg-white dark:text-black">Buka penjualan</span>
            </a>

            <a href="{{ route('penjualan.index', ['tipe' => 'Piutang', 'status' => 'Belum Lunas']) }}" class="app-card group flex flex-col justify-between transition duration-150 hover:-translate-y-0.5 hover:shadow-xl">
                <div class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">Belum lunas</p>
                    <div class="flex items-end justify-between gap-4">
                        <span class="text-4xl font-semibold tracking-tight">{{ $countPiutang }}</span>
                        <span class="app-badge">Receivables</span>
                    </div>
                </div>
                <span class="mt-6 inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white dark:border-white dark:bg-white dark:text-black">Buka Piutang</span>
            </a>

            <a href="{{ route('level.index') }}" class="app-card group flex flex-col justify-between transition duration-150 hover:-translate-y-0.5 hover:shadow-xl">
                <div class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">Perizinan</p>
                    <div class="flex items-end justify-between gap-4">
                        <span class="text-4xl font-semibold tracking-tight">{{ $countReq }}</span>
                        <span class="app-badge">Access control</span>
                    </div>
                </div>
                <span class="mt-6 inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white dark:border-white dark:bg-white dark:text-black">Atur izin</span>
            </a>
        </div>

        <section class="app-panel p-6 sm:p-8">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold tracking-tight">Grafik Keuntungan</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">12 bulan terakhir</p>
                </div>
                <a href="{{ route('laporan.profit') }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Buka Laporan</a>
            </div>
            <div class="h-72">
                <canvas id="profitChart"></canvas>
            </div>
        </section>

        <section class="app-panel p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="text-lg font-semibold tracking-tight">Grafik Penjualan</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">12 bulan terakhir</p>
            </div>
            <div class="h-72">
                <canvas id="penjualanChart"></canvas>
            </div>
        </section>

        <section class="app-panel p-6 sm:p-8">
            <div class="mb-6">
                <h3 class="text-lg font-semibold tracking-tight">Grafik Pembelian</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">12 bulan terakhir</p>
            </div>
            <div class="h-72">
                <canvas id="pembelianChart"></canvas>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function loadChartJs(callback) {
                if (window.Chart) {
                    callback();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = callback;
                document.head.appendChild(script);
            }

            let dashboardCharts = [];

            function renderDashboardCharts() {
                const penjualanCanvas = document.getElementById('penjualanChart');
                const pembelianCanvas = document.getElementById('pembelianChart');
                const profitCanvas = document.getElementById('profitChart');

                if (!penjualanCanvas || !pembelianCanvas || !profitCanvas) {
                    return;
                }

                const rootStyles = getComputedStyle(document.documentElement);
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = rootStyles.getPropertyValue('--app-text-muted').trim() || '#525252';
                const gridColor = rootStyles.getPropertyValue('--app-border').trim() || '#d4d4d4';
                const lineColor = rootStyles.getPropertyValue('--app-text').trim() || '#111111';
                const surfaceColor = rootStyles.getPropertyValue('--app-surface').trim() || '#ffffff';
                const months = @json($months);
                const penjualanData = @json($penjualanData);
                const pembelianData = @json($pembelianData);
                const profitData = @json($profitData);

                const chartPalette = isDark
                    ? {
                        sales: '#60a5fa',
                        purchases: '#fbbf24',
                        profit: '#34d399',
                    }
                    : {
                        sales: '#2563eb',
                        purchases: '#d97706',
                        profit: '#059669',
                    };

                dashboardCharts.forEach(function (chart) {
                    chart.destroy();
                });
                dashboardCharts = [];

                const sharedOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: textColor }, grid: { color: gridColor } },
                        y: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } },
                    },
                    plugins: {
                        legend: { labels: { color: textColor } },
                        tooltip: {
                            backgroundColor: surfaceColor,
                            titleColor: lineColor,
                            bodyColor: lineColor,
                            borderColor: gridColor,
                            borderWidth: 1,
                        },
                    },
                };

                dashboardCharts.push(new Chart(penjualanCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Penjualan',
                            data: penjualanData,
                            backgroundColor: chartPalette.sales + '22',
                            borderColor: chartPalette.sales,
                            pointBackgroundColor: chartPalette.sales,
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.25,
                            fill: true,
                        }],
                    },
                    options: sharedOptions,
                }));

                dashboardCharts.push(new Chart(pembelianCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Pembelian',
                            data: pembelianData,
                            backgroundColor: chartPalette.purchases + '22',
                            borderColor: chartPalette.purchases,
                            pointBackgroundColor: chartPalette.purchases,
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.25,
                            fill: true,
                        }],
                    },
                    options: sharedOptions,
                }));

                dashboardCharts.push(new Chart(profitCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Profit',
                            data: profitData,
                            backgroundColor: chartPalette.profit + '22',
                            borderColor: chartPalette.profit,
                            pointBackgroundColor: chartPalette.profit,
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.25,
                            fill: true,
                        }],
                    },
                    options: sharedOptions,
                }));
            }

            document.addEventListener('DOMContentLoaded', function () {
                loadChartJs(renderDashboardCharts);
            });

            window.addEventListener('theme-changed', function () {
                loadChartJs(renderDashboardCharts);
            });
        </script>
    @endpush
</x-app-layout>