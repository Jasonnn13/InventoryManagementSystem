<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Penjualan</h2>
            </div>
            <a href="{{ route('penjualan.create') }}" class="inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition duration-150 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:focus:ring-white">Tambah Penjualan</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <form action="{{ route('penjualan.index') }}" method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
                <div class="sm:col-span-2 lg:col-span-5">
                    <x-input-label for="search" value="Cari Pelanggan" />
                    <x-text-input id="search" name="search" type="text" class="mt-1" placeholder="Nama pelanggan" value="{{ $search }}" />
                </div>

                <div class="lg:col-span-2">
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface)] px-3 py-2 text-sm shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500 dark:focus:border-neutral-400 dark:focus:ring-neutral-400">
                        <option value="">Semua Status</option>
                        <option value="Lunas" {{ $status === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Belum Lunas" {{ $status === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <x-input-label for="month" value="Bulan" />
                    <select id="month" name="month" class="mt-1 block w-full rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface)] px-3 py-2 text-sm shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500 dark:focus:border-neutral-400 dark:focus:ring-neutral-400">
                        <option value="">Semua</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ (string)$month === (string)$m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('M') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <x-input-label for="year" value="Tahun" />
                    <select id="year" name="year" class="mt-1 block w-full rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface)] px-3 py-2 text-sm shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500 dark:focus:border-neutral-400 dark:focus:ring-neutral-400">
                        <option value="">Semua</option>
                        @foreach(range(\Carbon\Carbon::now()->year, \Carbon\Carbon::now()->year - 4, -1) as $y)
                            <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2 sm:justify-end">
                    <x-primary-button type="submit">Cari</x-primary-button>
                    <a href="{{ route('penjualan.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Reset</a>
                </div>
            </form>
        </section>

        <section class="app-panel overflow-visible">
            <div class="border-b border-[color:var(--app-border)] px-6 py-4">
                <h3 class="text-lg font-semibold tracking-tight">List Penjualan</h3>
            </div>

            <div class="overflow-x-auto overflow-y-visible">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Netto</th>
                            <th>Status</th>
                            <th>Tipe</th>
                            <th>Tipe Invoice</th>
                            <th>Sales</th>
                            <th>Tenggat</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th class="w-80 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualans as $penjualan)
                            <tr data-created-by="{{ $penjualan->user->id }}" class="cursor-pointer {{ $penjualan->status == 'Belum Lunas' && \Carbon\Carbon::now()->gt($penjualan->tenggat_waktu) ? 'bg-neutral-50 dark:bg-neutral-900/60' : '' }}" onclick="window.location='{{ route('rincianpenjualan.index', ['penjualan_id' => $penjualan->id]) }}'">
                                <td class="font-medium text-neutral-900 dark:text-neutral-100 whitespace-nowrap">{{ $penjualan->customer->name }}</td>
                                <td class="whitespace-nowrap">Rp. {{ number_format($penjualan->total_netto, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->status }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->tipe }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->tipe_ppn ?? 'Non PPN' }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->sales }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->tenggat_waktu->format('d-m-Y') }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->user->name }}</td>
                                <td class="whitespace-nowrap">{{ $penjualan->created_at->format('d-m-Y') }}</td>
                                <td onclick="event.stopPropagation()">
                                    <div class="flex items-center gap-2 whitespace-nowrap">
                                        <button type="button" data-download-toggle="{{ $penjualan->id }}" data-invoice-url="{{ route('rincianpenjualan.invoice', $penjualan->id) }}" data-surat-jalan-url="{{ route('rincianpenjualan.suratjalan', $penjualan->id) }}" class="download-toggle cursor-pointer rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900">Unduh</button>

                                        <a href="{{ route('penjualan.edit', $penjualan->id) }}" class="edit-link inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Ubah</a>

                                        @if (Auth::user()->level >= 2)
                                            <form action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST" onsubmit="return confirmation(event, this)">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit" class="delete-button">Hapus</x-danger-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-neutral-500 dark:text-neutral-400">No sales records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($penjualans->hasPages())
                <div class="border-t border-[color:var(--app-border)] px-6 py-4">
                    {{ $penjualans->appends(['search' => $search, 'status' => $status, 'month' => $month, 'year' => $year])->links() }}
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

            function permissionDenied(message) {
                runSwal({
                    title: message,
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'error',
                    button: 'OK',
                });

                return false;
            }

            function confirmation(event, form) {
                event.preventDefault();
                event.stopPropagation();

                runSwal({
                    title: 'Hapus data ini?',
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

            let downloadMenu = null;

            function closeDownloadMenu() {
                if (downloadMenu) {
                    downloadMenu.remove();
                    downloadMenu = null;
                }
            }

            function openDownloadMenu(toggle) {
                closeDownloadMenu();

                const menu = document.createElement('div');
                menu.className = 'fixed z-[9999] w-56 overflow-hidden rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface)] shadow-[var(--app-shadow)]';
                menu.innerHTML = `
                    <a href="${toggle.dataset.invoiceUrl}" class="block px-4 py-3 text-sm text-neutral-900 transition hover:bg-neutral-50 dark:text-neutral-100 dark:hover:bg-neutral-900">Unduh Invoice</a>
                    <a href="${toggle.dataset.suratJalanUrl}" class="block px-4 py-3 text-sm text-neutral-900 transition hover:bg-neutral-50 dark:text-neutral-100 dark:hover:bg-neutral-900">Unduh Surat Jalan</a>
                `;

                document.body.appendChild(menu);

                const rect = toggle.getBoundingClientRect();
                const menuWidth = 224;
                const left = Math.max(12, Math.min(rect.right - menuWidth, window.innerWidth - menuWidth - 12));
                const top = Math.min(rect.bottom + 8, window.innerHeight - 120);

                menu.style.left = `${left}px`;
                menu.style.top = `${top}px`;

                downloadMenu = menu;

                requestAnimationFrame(function () {
                    document.addEventListener('click', handleOutsideDownloadMenu, { once: true });
                });
            }

            function handleOutsideDownloadMenu(event) {
                if (downloadMenu && !downloadMenu.contains(event.target) && !event.target.closest('[data-download-toggle]')) {
                    closeDownloadMenu();
                    return;
                }

                if (downloadMenu && event.target.closest('[data-download-toggle]')) {
                    closeDownloadMenu();
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.download-toggle').forEach(function (toggle) {
                    toggle.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        openDownloadMenu(toggle);
                    });
                });
            });

            document.addEventListener('click', function (event) {
                if (event.target.closest('.download-menu') || event.target.closest('[data-download-toggle]')) {
                    return;
                }

                closeDownloadMenu();
            });

            window.addEventListener('resize', closeDownloadMenu);
            window.addEventListener('scroll', closeDownloadMenu, true);

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('tbody tr[data-created-by]').forEach(function (row) {
                    const editButton = row.querySelector('.edit-link');
                    const deleteButton = row.querySelector('.delete-button');

                    if (userLevel < 2) {
                        if (deleteButton && !deleteButton.hasAttribute('onsubmit')) {
                            deleteButton.addEventListener('click', function (event) {
                                event.preventDefault();
                                event.stopPropagation();
                                permissionDenied('Maaf, Anda tidak memiliki izin untuk menghapus penjualan ini');
                            });
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>

