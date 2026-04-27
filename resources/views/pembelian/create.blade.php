<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Create Pembelian</h2>
            </div>
            <a href="{{ route('pembelian.index') }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="app-card">
            <form action="{{ route('pembelian.store') }}" method="POST" class="grid gap-5 sm:grid-cols-2">
                @csrf

                <div>
                    <x-input-label for="suppliers_id" value="Supplier" />
                    <select id="suppliers_id" name="suppliers_id" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('suppliers_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="gudangs_id" value="Gudang" />
                    <select id="gudangs_id" name="gudangs_id" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        @foreach($gudangs as $gudang)
                            <option value="{{ $gudang->id }}" @selected(old('gudangs_id') == $gudang->id)>{{ $gudang->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex flex-wrap gap-3">
                    <x-primary-button type="submit">Lanjut</x-primary-button>
                    <a href="{{ route('pembelian.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Batal</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
