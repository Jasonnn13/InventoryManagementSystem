<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Tambah item pembelian</h2>
            </div>
            <a href="{{ route('rincianpembelian.index', $pembelian->id) }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <form action="{{ route('rincianpembelian.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="pembelian_id" value="{{ $pembelian->id }}">
                <input type="hidden" name="supplier_id" value="{{ $pembelian->suppliers_id }}">

                <div class="flex flex-wrap gap-3">
                    <x-primary-button type="button" onclick="addNewItem()">Tambah item baru</x-primary-button>
                    <x-primary-button type="button" onclick="addExistingItems()">Tambah item lama</x-primary-button>
                    <x-primary-button type="submit">Simpan item</x-primary-button>
                </div>

                <div id="items-container" class="space-y-4"></div>
                <div id="new-items-container" class="space-y-4"></div>

                <div class="app-card p-5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="total">Harga akhir</label>
                            <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white price-input" type="text" inputmode="numeric" id="total" name="total" placeholder="Masukkan harga akhir manual">
                            <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">Tetap isi manual. Sistem kasih sugesti dari total quantity x harga item.</p>
                        </div>
                        <div class="rounded-xl border border-dashed border-neutral-300 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-900/50">
                            <p class="text-xs uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Sugesti harga akhir</p>
                            <p id="suggested-total" class="mt-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">Rp 0</p>
                            <button type="button" id="use-suggested-total" class="mt-3 inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Gunakan sugesti</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
        <script>
            let existingItemsAdded = 0;
            let newItemIndex = 0;

            function addNewItem() {
                const container = document.getElementById('new-items-container');
                const block = `
                    <div class="app-card p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold">Item baru</h3>
                            <button type="button" class="rounded-full border border-neutral-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 hover:border-black hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900" onclick="removeItem(this)">Hapus</button>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="new-item-name-${newItemIndex}">Nama item</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="text" id="new-item-name-${newItemIndex}" name="items[new][${newItemIndex}][name]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="new-item-quantity-${newItemIndex}">Jumlah</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="number" min="1" id="new-item-quantity-${newItemIndex}" name="items[new][${newItemIndex}][quantity]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="new-item-price-${newItemIndex}">Harga (Rp.)</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white price-input" type="text" inputmode="numeric" id="new-item-price-${newItemIndex}" name="items[new][${newItemIndex}][price]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="kode-${newItemIndex}">Kode</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="text" id="kode-${newItemIndex}" name="items[new][${newItemIndex}][kode]" required>
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', block);
                newItemIndex += 1;
                updateSuggestedTotal();
            }

            function addExistingItems() {
                const container = document.getElementById('items-container');
                const block = `
                    <div class="app-card p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold">Item yang sudah ada</h3>
                            <button type="button" class="rounded-full border border-neutral-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 hover:border-black hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900" onclick="removeItem(this)">Hapus</button>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="stock-input-${existingItemsAdded}">Pilih item yang sudah ada</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="text" id="stock-input-${existingItemsAdded}" name="items[existing][${existingItemsAdded}][name]" placeholder="Masukkan nama item" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="quantity-${existingItemsAdded}">Jumlah</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="number" min="1" id="quantity-${existingItemsAdded}" name="items[existing][${existingItemsAdded}][quantity]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="price-${existingItemsAdded}">Harga (Rp.)</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white price-input" type="text" inputmode="numeric" id="price-${existingItemsAdded}" name="items[existing][${existingItemsAdded}][price]" required>
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', block);
                const currentIndex = existingItemsAdded;
                existingItemsAdded += 1;
                updateSuggestedTotal();

                $(`#stock-input-${currentIndex}`).autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: '/stocks/autocomplete',
                            dataType: 'json',
                            data: { term: request.term },
                            success: function (data) {
                                response(data);
                            },
                        });
                    },
                    select: function (event, ui) {
                        $(this).val(ui.item.label);

                        const priceInput = document.getElementById(`price-${currentIndex}`);
                        if (priceInput) {
                            const formattedPrice = formatIDR(String(ui.item.price || 0));
                            priceInput.placeholder = formattedPrice ? `Harga beli sekarang: ${formattedPrice}` : 'Masukkan harga';
                        }
                    },
                });
            }

            function removeItem(button) {
                button.closest('.app-card').remove();
                updateSuggestedTotal();
            }

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

            function toInt(value) {
                const parsed = parseInt((value || '').toString().replace(/\D/g, ''), 10);
                return Number.isNaN(parsed) ? 0 : parsed;
            }

            function updateSuggestedTotal() {
                let total = 0;

                document.querySelectorAll('input[name*="[quantity]"]').forEach((quantityInput) => {
                    const name = quantityInput.getAttribute('name') || '';
                    const prefix = name.replace('[quantity]', '');
                    const priceInput = document.querySelector(`input[name="${prefix}[price]"]`);

                    const quantity = toInt(quantityInput.value);
                    const price = toInt(priceInput ? priceInput.value : '0');
                    total += quantity * price;
                });

                const suggestedTotal = document.getElementById('suggested-total');
                if (suggestedTotal) {
                    suggestedTotal.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
                }

                return total;
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
                    
                    // Store actual numeric value in a data attribute
                    input.setAttribute('data-value', numericValue);
                    
                    // Adjust cursor position
                    const newLength = input.value.length;
                    const diff = newLength - oldLength;
                    input.setSelectionRange(cursorPos + diff, cursorPos + diff);
                }

                if (
                    e.target.classList.contains('price-input') ||
                    (e.target.getAttribute('name') || '').includes('[quantity]')
                ) {
                    updateSuggestedTotal();
                }
            });

            document.getElementById('use-suggested-total').addEventListener('click', function () {
                const suggested = updateSuggestedTotal();
                const totalInput = document.getElementById('total');
                if (totalInput) {
                    totalInput.value = formatIDR(String(suggested));
                    totalInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                updateSuggestedTotal();
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
