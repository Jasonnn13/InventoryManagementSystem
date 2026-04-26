<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Create Penjualan</h2>
            </div>
            <a href="{{ route('penjualan.index') }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="app-card">
            <form action="{{ route('penjualan.store') }}" method="POST" class="grid gap-5 sm:grid-cols-2">
                @csrf

                <div>
                    <x-input-label for="customers_id" value="Customer" />
                    <select id="customers_id" name="customers_id" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        <option value="Lunas">Lunas</option>
                        <option value="Belum Lunas">Belum Lunas</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="tipe" value="Tipe" />
                    <select id="tipe" name="tipe" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        <option value="Cash">Cash</option>
                        <option value="Piutang">Piutang</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="tipe_ppn" value="Tipe Invoice" />
                    <select id="tipe_ppn" name="tipe_ppn" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        <option value="PPN">PPN</option>
                        <option value="Non PPN" selected>Non PPN</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="sales" value="Sales" />
                    <x-text-input id="sales" name="sales" type="text" class="mt-2" required />
                </div>

                <div>
                    <x-input-label for="tenggat_waktu" value="Tenggat" />
                    <x-text-input id="tenggat_waktu" name="tenggat_waktu" type="date" class="mt-2" :value="old('tenggat_waktu')" required />
                </div>

                <div>
                    <x-input-label for="diskon" value="Diskon (%)" />
                    <x-text-input id="diskon" name="diskon" type="number" min="0" max="100" class="mt-2" :value="old('diskon', 0)" required />
                </div>

                <div class="sm:col-span-2 flex flex-wrap gap-3">
                    <x-primary-button type="submit">Simpan</x-primary-button>
                    <a href="{{ route('penjualan.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Batal</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
