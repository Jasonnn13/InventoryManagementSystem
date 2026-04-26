<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Ubah Stok</h2>
            </div>
            <a href="{{ route('stocks.index') }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="app-card">
            <form action="{{ route('stocks.update', $stock->id) }}" method="POST" class="grid gap-5 sm:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="sm:col-span-2">
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-2" value="{{ old('name', $stock->stock->name) }}" required />
                    @error('name')
                        <p class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="kode" value="Kode" />
                    <x-text-input id="kode" name="kode" type="text" class="mt-2" value="{{ old('kode', $stock->stock->kode) }}" required />
                    @error('kode')
                        <p class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="stock" value="Stocks" />
                    <x-text-input id="stock" name="stock" type="number" class="mt-2" value="{{ old('stock', $stock->quantity) }}" required />
                    @error('stock')
                        <p class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="beli" value="Harga Beli (Rp.)" />
                    <x-text-input id="beli" class="price-input" inputmode="numeric" name="beli" type="text" class="mt-2" value="{{ old('beli', number_format($stock->stock->beli, 0, ',', '.')) }}" required />
                    @error('beli')
                        <p class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="jual" value="Harga Jual (Rp.)" />
                    <x-text-input id="jual" class="price-input" inputmode="numeric" name="jual" type="text" class="mt-2" value="{{ old('jual', number_format($stock->stock->jual, 0, ',', '.')) }}" required />
                    @error('jual')
                        <p class="mt-2 text-sm text-neutral-700 dark:text-neutral-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 flex flex-wrap gap-3">
                    <x-primary-button type="submit">Perbarui Stok</x-primary-button>
                    <a href="{{ route('stocks.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Kembali</a>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script>
            // Format currency as IDR
            function formatIDR(value) {
                if (!value) return '';
                const num = value.replace(/\D/g, '');
                if (!num) return '';
                return new Intl.NumberFormat('id-ID').format(num);
            }

            // Get numeric value from formatted input
            function getNumericValue(formattedValue) {
                return formattedValue.replace(/\D/g, '');
            }

            // Add event listeners to price inputs
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('price-input')) {
                    const input = e.target;
                    const cursorPos = input.selectionStart;
                    const oldLength = input.value.length;
                    
                    // Get numeric value and format it
                    const numericValue = getNumericValue(input.value);
                    const formattedValue = formatIDR(input.value);
                    
                    // Update display
                    input.value = formattedValue;
                    
                    // Adjust cursor position
                    const newLength = input.value.length;
                    const diff = newLength - oldLength;
                    input.setSelectionRange(cursorPos + diff, cursorPos + diff);
                }
            });

            // Before form submission, restore numeric values
            document.querySelector('form').addEventListener('submit', function(e) {
                document.querySelectorAll('.price-input').forEach(input => {
                    const numericValue = getNumericValue(input.value);
                    input.value = numericValue;
                });
            });
        </script>
    @endpush
</x-app-layout>
