<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Laporan Keuangan</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold tracking-tight">Keuangan Bulanan</h3>
                <form action="{{ route('laporan.profit') }}" method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-end gap-3">
                    <div class="w-32">
                        <x-input-label for="year" value="Tahun" />
                        <select id="year" name="year" class="mt-1 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-2 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" @selected($selectedYear == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button type="submit">Tampilkan</x-primary-button>
                </form>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($laporans as $laporan)
                    <article class="rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-alt)] p-5 shadow-[var(--app-shadow)]">
                        <p class="text-sm uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">{{ strtolower(\Carbon\Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1)->format('M')) }} {{ $laporan->tahun }}</p>
                        <div class="mt-4 space-y-2 text-sm">
                            <p><span class="font-semibold text-neutral-900 dark:text-neutral-100">Pengeluaran:</span> Rp. {{ number_format($laporan->pengeluaran, 0, ',', '.') }}</p>
                            <p><span class="font-semibold text-neutral-900 dark:text-neutral-100">Pemasukan:</span> Rp. {{ number_format($laporan->pemasukan, 0, ',', '.') }}</p>
                            <p><span class="font-semibold text-neutral-900 dark:text-neutral-100">Profit:</span> Rp. {{ number_format($laporan->profit, 0, ',', '.') }}</p>
                        </div>
                    </article>
                @empty
                    <p class="text-neutral-500 dark:text-neutral-400">No profit data available.</p>
                @endforelse
            </div>
        </section>

        <section class="app-panel p-6">
            <h3 class="text-lg font-semibold tracking-tight">Trend Overview</h3>
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-alt)] p-4 shadow-[var(--app-shadow)]">
                    <canvas id="profitChart" height="180"></canvas>
                </div>
                <div class="rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-alt)] p-4 shadow-[var(--app-shadow)]">
                    <canvas id="cashflowChart" height="180"></canvas>
                </div>
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

            let laporanCharts = [];

            function renderLaporanCharts() {
                const profitCanvas = document.getElementById('profitChart');
                const cashflowCanvas = document.getElementById('cashflowChart');

                if (!profitCanvas || !cashflowCanvas) {
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

                laporanCharts.forEach(function (chart) {
                    chart.destroy();
                });
                laporanCharts = [];

                const sharedOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: textColor }, grid: { color: gridColor } },
                        y: { ticks: { color: textColor }, grid: { color: gridColor } },
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

                laporanCharts.push(new Chart(profitCanvas.getContext('2d'), {
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

                laporanCharts.push(new Chart(cashflowCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: penjualanData,
                                backgroundColor: chartPalette.sales + '22',
                                borderColor: chartPalette.sales,
                                pointBackgroundColor: chartPalette.sales,
                                borderWidth: 2,
                                pointRadius: 3,
                                tension: 0.25,
                                fill: true,
                            },
                            {
                                label: 'Pembelian',
                                data: pembelianData,
                                backgroundColor: chartPalette.purchases + '22',
                                borderColor: chartPalette.purchases,
                                pointBackgroundColor: chartPalette.purchases,
                                borderWidth: 2,
                                pointRadius: 3,
                                tension: 0.25,
                                fill: true,
                            },
                        ],
                    },
                    options: sharedOptions,
                }));
            }

            document.addEventListener('DOMContentLoaded', function () {
                loadChartJs(renderLaporanCharts);
            });

            window.addEventListener('theme-changed', function () {
                loadChartJs(renderLaporanCharts);
            });
        </script>
    @endpush
</x-app-layout>
