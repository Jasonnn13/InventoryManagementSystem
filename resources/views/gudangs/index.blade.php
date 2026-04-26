<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Gudang</h2>
            </div>
            <a href="{{ route('gudangs.create') }}" class="inline-flex items-center justify-center rounded-full border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition duration-150 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:focus:ring-white">Tambah Gudang</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <form action="{{ route('gudangs.index') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-input-label for="search" value="Cari nama" />
                    <x-text-input id="search" name="search" type="text" class="mt-2" placeholder="Ketik nama gudang" value="{{ $search }}" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button type="submit">Cari</x-primary-button>
                    <a href="{{ route('gudangs.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Atur ulang</a>
                </div>
            </form>
        </section>

        <section class="app-panel overflow-hidden">
            <div class="border-b border-[color:var(--app-border)] px-6 py-4">
                <h3 class="text-lg font-semibold tracking-tight">Daftar Gudang</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="w-56 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gudangs as $gudang)
                            <tr>
                                <td class="font-medium text-neutral-900 dark:text-neutral-100">{{ $gudang->name }}</td>
                                <td>
                                    <div class="flex flex-nowrap items-center gap-2 whitespace-nowrap">
                                        <a href="{{ route('gudangs.edit', $gudang->id) }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Ubah</a>
                                        <form action="{{ route('gudangs.destroy', $gudang->id) }}" method="POST" onsubmit="return confirmation(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit" class="delete-button">Hapus</x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-neutral-500 dark:text-neutral-400">Belum ada gudang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($gudangs->hasPages())
                <div class="border-t border-[color:var(--app-border)] px-6 py-4">
                    {{ $gudangs->appends(['search' => $search])->links() }}
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
                        title: "Anda tidak punya izin menghapus gudang ini",
                        text: 'Tindakan ini tidak dapat dibatalkan.',
                        icon: 'error',
                        button: 'OK',
                    });
                    return false;
                }

                runSwal({
                    title: 'Yakin mau hapus gudang ini?',
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
