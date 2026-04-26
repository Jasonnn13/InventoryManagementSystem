<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="app-page-title">Ubah Status Pengguna</h2>
                <p class="mt-2 max-w-2xl text-sm text-neutral-600 dark:text-neutral-400">
                    Gunakan halaman ini untuk mengubah request atau admin. Owner tidak bisa diubah dari halaman ini.
                </p>
            </div>
            <a href="{{ route('level.index') }}" class="text-sm font-medium text-neutral-900 underline decoration-neutral-400 underline-offset-4 dark:text-neutral-100">Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <section class="app-card">
            <form action="{{ route('level.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="level" value="Status" />
                    <select id="level" name="level" class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-black focus:ring-2 focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:focus:border-white dark:focus:ring-white" required>
                        <option value="0" @selected(old('level', $user->level) == 0)>Request</option>
                        <option value="1" @selected(old('level', $user->level) == 1)>Admin</option>
                    </select>
                    @error('level')
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-primary-button type="submit">Simpan perubahan</x-primary-button>
                    <a href="{{ route('level.index') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Batal</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
