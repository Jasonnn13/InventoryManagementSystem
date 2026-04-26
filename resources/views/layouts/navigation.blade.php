<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-[color:var(--app-border)] bg-[color:var(--app-surface)]/95 backdrop-blur">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 text-sm font-semibold uppercase tracking-[0.24em] text-neutral-900 dark:text-neutral-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full border border-black bg-black text-xs text-white dark:border-white dark:bg-white dark:text-black">I</span>
                        <span class="hidden sm:inline">{{ config('app.name', 'CVHaathee') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Beranda') }}
                    </x-nav-link>
                    <x-nav-link :href="route('stocks.index')" :active="request()->routeIs('stocks.*')">
                        {{ __('Stok') }}
                    </x-nav-link>
                    <x-nav-link :href="route('penjualan.index')" :active="request()->routeIs('penjualan.*') || request()->routeIs('rincianpenjualan.*')">
                        {{ __('Penjualan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('pembelian.index')" :active="request()->routeIs('pembelian.*') || request()->routeIs('rincianpembelian.*')">
                        {{ __('Pembelian') }}
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        {{ __('Mitra') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                <button type="button" onclick="toggleTheme()" class="inline-flex items-center gap-2 rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-muted)] px-4 py-2 text-sm font-medium text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white" aria-label="Ganti tema">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25M12 18.75V21M4.5 12H2.25M21.75 12H19.5M5.636 5.636L4.05 4.05m15.9 15.9-1.586-1.586M5.636 18.364L4.05 19.95m15.9-15.9-1.586 1.586M12 16.5a4.5 4.5 0 100-9 4.5 4.5 0 000 9z" />
                    </svg>
                    <span>Tema</span>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-muted)] px-4 py-2 text-sm font-medium text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full border border-black bg-black text-xs font-semibold text-white dark:border-white dark:bg-white dark:text-black">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <span>{{ Auth::user()->name }}</span>

                            <span class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-muted)] p-2 text-neutral-600 transition duration-150 hover:border-black hover:text-black focus:outline-none focus:ring-2 focus:ring-black dark:text-neutral-300 dark:hover:border-white dark:hover:text-white dark:focus:ring-white">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-[color:var(--app-border)] bg-[color:var(--app-surface)] sm:hidden">
        <div class="space-y-1 py-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stocks.index')" :active="request()->routeIs('stocks.*')">
                {{ __('Stok') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('penjualan.index')" :active="request()->routeIs('penjualan.*') || request()->routeIs('rincianpenjualan.*')">
                {{ __('Penjualan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pembelian.index')" :active="request()->routeIs('pembelian.*') || request()->routeIs('rincianpembelian.*')">
                {{ __('Pembelian') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                {{ __('Mitra') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-[color:var(--app-border)] py-4">
            <div class="px-4">
                <div class="text-base font-semibold text-neutral-900 dark:text-neutral-100">{{ Auth::user()->name }}</div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-1">
                <button type="button" onclick="toggleTheme()" class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-neutral-900 transition duration-150 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-black dark:text-neutral-100 dark:hover:bg-neutral-900 dark:focus:ring-white" aria-label="Ganti tema">
                    Ganti tema
                </button>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
