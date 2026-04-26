<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Tambah item penjualan</h2>
            </div>
            <a href="{{ route('rincianpenjualan.index', $penjualan->id) }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="app-panel p-6">
            <form id="penjualan-form" action="{{ route('rincianpenjualan.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="penjualan_id" value="{{ $penjualan->id }}">

                @if ($errors->any())
                    <div class="rounded-2xl border border-neutral-300 bg-neutral-50 p-4 text-sm text-neutral-700 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-wrap gap-3">
                    <x-primary-button type="button" onclick="addExistingItems()">Tambah item lama</x-primary-button>
                    <x-primary-button type="submit">Simpan item</x-primary-button>
                </div>

                <div id="items-container" class="space-y-4"></div>
                <div id="warning-message" class="hidden rounded-2xl border border-neutral-300 bg-neutral-50 p-4 text-sm text-neutral-700 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">Periksa item: pilih barang, pilih gudang yang punya stok, dan pastikan jumlah tidak melebihi stok.</div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
        <script>
            let existingItemsAdded = 0;

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
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="text" id="stock-input-${existingItemsAdded}" name="items[${existingItemsAdded}][name]" placeholder="Masukkan nama item" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="gudangs_id-${existingItemsAdded}">Gudang</label>
                                <select class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black disabled:cursor-not-allowed disabled:opacity-60 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" id="gudangs_id-${existingItemsAdded}" name="items[${existingItemsAdded}][gudangs_id]" required disabled>
                                    <option value="">Pilih barang dulu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300" for="quantity-${existingItemsAdded}">Jumlah</label>
                                <input class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" type="number" min="1" id="quantity-${existingItemsAdded}" name="items[${existingItemsAdded}][quantity]" required>
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', block);
                const currentIndex = existingItemsAdded;
                existingItemsAdded += 1;

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
                        $(this).attr('data-stock-id', ui.item.id || '');
                        $(this).attr('data-quantity', ui.item.quantity);
                        loadGudangOptions(currentIndex, ui.item.id);
                    },
                    change: function () {
                        const stockId = $(this).attr('data-stock-id');
                        if (!stockId) {
                            resetGudangOptions(currentIndex);
                        }
                    },
                });

                document.getElementById(`stock-input-${currentIndex}`).addEventListener('input', function () {
                    this.setAttribute('data-stock-id', '');
                    this.setAttribute('data-quantity', '0');
                    resetGudangOptions(currentIndex);
                });
            }

            function resetGudangOptions(index) {
                const gudangSelect = document.getElementById(`gudangs_id-${index}`);
                if (!gudangSelect) {
                    return;
                }

                gudangSelect.disabled = true;
                gudangSelect.innerHTML = '<option value="">Pilih barang dulu</option>';
            }

            function loadGudangOptions(index, stockId) {
                const gudangSelect = document.getElementById(`gudangs_id-${index}`);
                if (!gudangSelect) {
                    return;
                }

                gudangSelect.disabled = true;
                gudangSelect.innerHTML = '<option value="">Memuat gudang...</option>';

                fetch(`/stocks/${stockId}/available-gudangs`)
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Gagal memuat data gudang.');
                        }
                        return response.json();
                    })
                    .then(function (gudangs) {
                        if (!Array.isArray(gudangs) || gudangs.length === 0) {
                            gudangSelect.disabled = true;
                            gudangSelect.innerHTML = '<option value="">Tidak ada stok di gudang mana pun</option>';
                            return;
                        }

                        gudangSelect.disabled = false;
                        gudangSelect.innerHTML = '<option value="">Pilih gudang</option>';
                        gudangs.forEach(function (gudang) {
                            const option = document.createElement('option');
                            option.value = gudang.id;
                            option.textContent = `${gudang.name} (stok: ${gudang.quantity})`;
                            option.setAttribute('data-quantity', String(gudang.quantity || 0));
                            gudangSelect.appendChild(option);
                        });
                    })
                    .catch(function () {
                        gudangSelect.disabled = true;
                        gudangSelect.innerHTML = '<option value="">Gagal memuat gudang</option>';
                    });
            }

            function removeItem(button) {
                button.closest('.app-card').remove();
            }

            function validateQuantities() {
                let isValid = true;
                document.querySelectorAll('#items-container .app-card').forEach(function (item) {
                    const stockInput = item.querySelector('input[name$="[name]"]');
                    const quantityInput = item.querySelector('input[type="number"]');
                    const gudangSelect = item.querySelector('select[name$="[gudangs_id]"]');
                    const stockId = stockInput ? stockInput.getAttribute('data-stock-id') : null;

                    if (!stockId || !gudangSelect || gudangSelect.disabled || !gudangSelect.value) {
                        isValid = false;
                        return;
                    }

                    const selectedOption = gudangSelect ? gudangSelect.options[gudangSelect.selectedIndex] : null;
                    const availableQuantity = parseInt((selectedOption && selectedOption.getAttribute('data-quantity')) || stockInput.getAttribute('data-quantity') || '0', 10);
                    const quantity = parseInt(quantityInput.value || '0', 10);

                    if (quantity > availableQuantity) {
                        isValid = false;
                    }
                });

                document.getElementById('warning-message').classList.toggle('hidden', isValid);
                return isValid;
            }

            document.getElementById('penjualan-form').addEventListener('submit', function (event) {
                if (!validateQuantities()) {
                    event.preventDefault();
                }
            });
        </script>
    @endpush
</x-app-layout>
