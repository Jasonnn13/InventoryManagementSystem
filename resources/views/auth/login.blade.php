<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center sm:text-left">
            <h2 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">Masuk</h2>
            <p class="text-sm leading-6 text-neutral-600 dark:text-neutral-400">Masuk untuk mengelola stok, pesanan, dan persetujuan.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if ($errors->any())
            <div class="rounded-2xl border border-black/10 bg-neutral-100 p-4 dark:border-white/10 dark:bg-neutral-900">
                <x-input-error :messages="$errors->all()" />
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="mt-2" type="password" name="password" required autocomplete="current-password" />
            </div>

            <label for="remember_me" class="flex items-center gap-3 text-sm text-neutral-700 dark:text-neutral-300">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-neutral-300 text-black focus:ring-black dark:border-neutral-700 dark:bg-neutral-950 dark:text-white dark:focus:ring-white">
                <span>Ingat saya</span>
            </label>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-900 shadow-sm transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">Daftar</a>
                    @endif
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="inline-flex items-center justify-center rounded-full border border-transparent px-1 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-700 underline decoration-neutral-400 underline-offset-4 transition hover:text-black dark:text-neutral-300 dark:hover:text-white">Lupa kata sandi</a>
                    @endif
                </div>

                <x-primary-button type="submit">Masuk</x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
