<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CVHaathee') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script>
            function getStoredTheme() {
                try {
                    return localStorage.getItem('theme');
                } catch (e) {
                    return null;
                }
            }

            function setStoredTheme(theme) {
                try {
                    localStorage.setItem('theme', theme);
                } catch (e) {
                    // Ignore storage errors (private mode/restricted browser)
                }
            }

            function applyTheme(theme) {
                const root = document.documentElement;
                const isDark = theme === 'dark';

                root.classList.toggle('dark', isDark);
                root.style.colorScheme = isDark ? 'dark' : 'light';

                if (document.body) {
                    document.body.classList.toggle('dark', isDark);
                }

                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
            }

            (function () {
                const storedTheme = getStoredTheme();
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                applyTheme(storedTheme ? storedTheme : (prefersDark ? 'dark' : 'light'));
            })();

            window.toggleTheme = function () {
                const root = document.documentElement;
                const nextIsDark = !root.classList.contains('dark');
                const nextTheme = nextIsDark ? 'dark' : 'light';

                applyTheme(nextTheme);
                setStoredTheme(nextTheme);
            };
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased text-neutral-900 dark:text-neutral-100">
        <div class="min-h-screen bg-[var(--app-bg)]">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-[color:var(--app-border)] bg-[color:var(--app-surface)]/95 backdrop-blur">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
