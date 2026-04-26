<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Rincian Pembelian</h2>
            </div>
            <a href="{{ route('rincianpembelian.create', $pembelian->id) }}" class="inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition duration-150 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:focus:ring-white" id="addBtn">Tambah Item</a>
        </div>
    </x-slot>

    <div class="app-panel overflow-hidden">
        <div class="border-b border-[color:var(--app-border)] px-6 py-4">
            <h3 class="text-lg font-semibold tracking-tight">Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Stock Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th class="w-56 whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rincianpembelians as $rincian)
                        <tr data-created-by="{{ $rincian->pembelian->user->id }}">
                            <td class="font-medium text-neutral-900 dark:text-neutral-100">{{ $rincian->stock->name }}</td>
                            <td>{{ $rincian->quantity }}</td>
                            <td>Rp. {{ number_format($rincian->price, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($rincian->total, 0, ',', '.') }}</td>
                            <td onclick="event.stopPropagation()">
                                <div class="flex flex-nowrap items-center gap-2 whitespace-nowrap">
                                    <a href="{{ route('rincianpembelian.edit', $rincian->id) }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white edit">Ubah</a>
                                    <form action="{{ route('rincianpembelian.destroy', $rincian->id) }}" method="POST" onsubmit="return confirmation(event, this)">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button type="submit" class="delete">Hapus</x-danger-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-neutral-500 dark:text-neutral-400">No purchase details found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            const userLevel = @json(Auth::user()->level);
            const userId = @json(Auth::user()->id);

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
                    title: 'Apakah Anda yakin ingin menghapus data ini?',
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

            document.addEventListener('DOMContentLoaded', function () {
                const addButton = document.getElementById('addBtn');
                document.querySelectorAll('tbody tr[data-created-by]').forEach(function (row) {
                    const createdBy = parseInt(row.getAttribute('data-created-by'), 10);
                    const editButton = row.querySelector('.edit');
                    const deleteButton = row.querySelector('.delete');

                    if (userLevel === 1 && userId !== createdBy) {
                        if (editButton) {
                            editButton.addEventListener('click', function (event) {
                                event.preventDefault();
                                permissionDenied('Maaf, Anda tidak memiliki izin untuk mengubah rincian pembelian ini');
                            });
                        }
                        if (deleteButton) {
                            deleteButton.addEventListener('click', function (event) {
                                event.preventDefault();
                                permissionDenied('Maaf, Anda tidak memiliki izin untuk menghapus rincian pembelian ini');
                            });
                        }
                        if (addButton) {
                            addButton.addEventListener('click', function (event) {
                                event.preventDefault();
                                permissionDenied('Maaf, Anda tidak memiliki izin untuk menambah rincian pembelian ini');
                            });
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>

