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
    </head>
    <body class="font-sans text-neutral-900 antialiased dark:text-neutral-100">
        <div class="flex min-h-screen flex-col bg-[var(--app-bg)] px-4 py-10 sm:px-6 lg:px-8">
            <div class="mx-auto mb-4 flex w-full max-w-5xl justify-end">
                <button type="button" onclick="toggleTheme()" class="inline-flex items-center gap-2 rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-muted)] px-4 py-2 text-sm font-medium text-neutral-900 transition duration-150 hover:border-black hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 dark:text-neutral-100 dark:hover:border-white dark:hover:bg-neutral-900 dark:focus:ring-white" aria-label="Ganti tema">
                    Ganti tema
                </button>
            </div>
            <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col items-center justify-center">
                <div class="w-full max-w-md rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface)] p-6 shadow-[var(--app-shadow)] sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
