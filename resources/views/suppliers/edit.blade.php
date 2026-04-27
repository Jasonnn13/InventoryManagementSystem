<x-app-layout>
    @php
        $returnQuery = array_filter([
            'type' => request('type', 'supplier'),
            'search' => request('search'),
        ], fn ($value) => $value !== null && $value !== '');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Ubah Pemasok</h2>
            </div>
            <a href="{{ route('customers.index', $returnQuery) }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="app-card">
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="grid gap-5 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="{{ request('type', 'supplier') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-2" value="{{ old('name', $supplier->name) }}" required />
                </div>

                <div>
                    <x-input-label for="contact_information" value="Contact Information" />
                    <x-text-input id="contact_information" name="contact_information" type="text" class="mt-2" value="{{ old('contact_information', $supplier->contact_information) }}" required />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Address" />
                    <x-text-input id="address" name="address" type="text" class="mt-2" value="{{ old('address', $supplier->address) }}" required />
                </div>

                <div class="sm:col-span-2 flex flex-wrap gap-3">
                    <x-primary-button type="submit">Simpan perubahan</x-primary-button>
                    <a href="{{ route('customers.index', $returnQuery) }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Batal</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
