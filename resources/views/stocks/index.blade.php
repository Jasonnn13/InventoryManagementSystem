<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Stok</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('stocks.create') }}" class="inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition duration-150 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:focus:ring-white">Tambah Stok</a>
                <a href="{{ route('gudangs.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Gudang</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <form action="{{ route('stocks.index') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-input-label for="search" value="Cari nama" />
                    <x-text-input id="search" name="search" type="text" class="mt-2" placeholder="Ketik nama stok" value="{{ $search }}" />
                </div>

                <div class="flex-1">
                    <x-input-label for="gudang_id" value="Filter gudang" />
                    <select id="gudang_id" name="gudang_id" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white">
                        <option value="">Semua gudang</option>
                        @foreach($gudangs as $gudang)
                            <option value="{{ $gudang->id }}" @selected((string) $gudangId === (string) $gudang->id)>{{ $gudang->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <x-primary-button type="submit">Cari</x-primary-button>
                    <a href="{{ route('stocks.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Atur ulang</a>
                </div>
            </form>
        </section>

        @php
            $hasZeroHargaJual = false;
        @endphp

        @foreach($stocks as $stock)
            @if($stock->stock->jual == 0)
                @php $hasZeroHargaJual = true; @endphp
            @endif
        @endforeach

        @if($hasZeroHargaJual)
            <div class="rounded-2xl border border-black/10 bg-neutral-100 px-4 py-3 text-sm text-neutral-800 dark:border-white/10 dark:bg-neutral-900 dark:text-neutral-200">
                Peringatan: ada stok yang masih punya harga jual nol. Harap perbarui.
            </div>
        @endif

        <section class="app-panel overflow-hidden">
            <div class="border-b border-[color:var(--app-border)] px-6 py-4">
                <h3 class="text-lg font-semibold tracking-tight">Daftar Stok</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Gudang</th>
                            <th>Kode</th>
                            <th>Jumlah</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th class="w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr class="cursor-pointer {{ ($stock->stock->jual == 0 || empty($stock->stock->jual)) ? 'bg-neutral-50 dark:bg-neutral-900/60' : '' }}" onclick="window.location='{{ route('stocks.history', $stock->stock->id) }}'">
                                <td class="font-medium text-neutral-900 dark:text-neutral-100">{{ $stock->stock->name }}</td>
                                <td>{{ $stock->gudang->name }}</td>
                                <td>{{ $stock->stock->kode }}</td>
                                <td>{{ $stock->quantity }}</td>
                                <td>Rp. {{ number_format($stock->stock->beli, 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($stock->stock->jual, 0, ',', '.') }}</td>
                                <td onclick="event.stopPropagation()">
                                    <div class="flex flex-wrap gap-2">
                                        @if (Auth::user()->level >= 2)
                                            <a href="{{ route('stocks.edit', $stock->id) }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Ubah</a>
                                            <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" onsubmit="return confirmation(event, this)">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit">Hapus</x-danger-button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-400">Owner only</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-neutral-500 dark:text-neutral-400">Belum ada data stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stocks->hasPages())
                <div class="border-t border-[color:var(--app-border)] px-6 py-4">
                    {{ $stocks->appends(['search' => $search, 'gudang_id' => $gudangId])->links() }}
                </div>
            @endif
        </section>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            const userLevel = @json(Auth::user()->level);

            function runSwal(options, onConfirm) {
                const invoke = function () {
                    const result = swal(options);
                    if (onConfirm) {
                        result.then(onConfirm);
                    }
                };

                if (typeof swal === 'function') {
                    invoke();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js';
                script.onload = invoke;
                document.head.appendChild(script);
            }

            function confirmation(event, form) {
                event.preventDefault();
                event.stopPropagation();

                if (userLevel < 2) {
                    runSwal({
                        title: "Anda tidak punya izin menghapus stok ini.",
                        text: 'Tindakan ini tidak dapat dibatalkan.',
                        icon: 'error',
                        button: 'OK'
                    });

                    return false;
                }

                runSwal({
                    title: 'Yakin mau hapus stok ini?',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }, function (willDelete) {
                    if (willDelete) {
                        form.submit();
                    }
                });

                return false;
            }
        </script>
    @endpush
</x-app-layout>

