<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Detail Stok</h2>
            </div>
            <a href="{{ route('stocks.index') }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke stok</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">Nama</dt>
                    <dd class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $stock->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">Kode</dt>
                    <dd class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $stock->kode }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">Total Stok</dt>
                    <dd class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $stock->stock }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">Supplier</dt>
                    <dd class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $stock->supplier->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">Harga Beli</dt>
                    <dd class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">Rp. {{ number_format($stock->beli, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-400">Harga Jual</dt>
                    <dd class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">Rp. {{ number_format($stock->jual, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </section>

        <section class="app-panel overflow-hidden">
            <div class="border-b border-[color:var(--app-border)] px-6 py-4">
                <h3 class="text-lg font-semibold tracking-tight">Stok Per Gudang</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Gudang</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gudangStocks as $gudangStock)
                            <tr>
                                <td class="font-medium text-neutral-900 dark:text-neutral-100">{{ $gudangStock->gudang->name ?? '-' }}</td>
                                <td>{{ $gudangStock->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-neutral-500 dark:text-neutral-400">Belum ada data gudang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="app-panel overflow-hidden">
            <div class="border-b border-[color:var(--app-border)] px-6 py-4">
                <h3 class="text-lg font-semibold tracking-tight">Histori Transaksi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Mitra</th>
                            <th>Gudang</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactionHistories as $history)
                            <tr>
                                <td>{{ $history['tanggal']->format('d-m-Y') }}</td>
                                <td>{{ $history['jenis'] }}</td>
                                <td class="font-medium text-neutral-900 dark:text-neutral-100">{{ $history['mitra'] }}</td>
                                <td>{{ $history['gudang'] }}</td>
                                <td>{{ $history['qty'] }}</td>
                                <td>Rp. {{ number_format($history['harga'], 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($history['total'], 0, ',', '.') }}</td>
                                <td>{{ $history['status'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-neutral-500 dark:text-neutral-400">Belum ada histori transaksi untuk item ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
