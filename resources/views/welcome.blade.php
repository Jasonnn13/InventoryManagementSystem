<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS2</title>
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
        }

        (function () {
            const storedTheme = getStoredTheme();
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(storedTheme ? storedTheme : (prefersDark ? 'dark' : 'light'));
        })();

        window.toggleTheme = function () {
            const root = document.documentElement;
            const nextTheme = root.classList.contains('dark') ? 'light' : 'dark';

            applyTheme(nextTheme);
            setStoredTheme(nextTheme);
        };
    </script>
    <style>
        :root {
            color-scheme: light;
            --welcome-bg-top: rgba(0, 0, 0, 0.05);
            --welcome-bg-main-1: #f5f5f5;
            --welcome-bg-main-2: #ffffff;
            --welcome-bg-main-3: #efefef;
            --welcome-text: #111111;
            --welcome-muted: rgba(17, 17, 17, 0.72);
            --welcome-border: rgba(0, 0, 0, 0.12);
            --welcome-panel: rgba(255, 255, 255, 0.88);
            --welcome-panel-alt: rgba(0, 0, 0, 0.03);
            --welcome-shadow: 0 30px 100px rgba(0, 0, 0, 0.16);
            --welcome-primary-bg: #111111;
            --welcome-primary-text: #ffffff;
            --welcome-ghost-text: #111111;
        }

        html.dark {
            color-scheme: dark;
            --welcome-bg-top: rgba(255, 255, 255, 0.08);
            --welcome-bg-main-1: #050505;
            --welcome-bg-main-2: #171717;
            --welcome-bg-main-3: #0c0c0c;
            --welcome-text: #f5f5f5;
            --welcome-muted: rgba(255, 255, 255, 0.72);
            --welcome-border: rgba(255, 255, 255, 0.12);
            --welcome-panel: rgba(18, 18, 18, 0.88);
            --welcome-panel-alt: rgba(255, 255, 255, 0.03);
            --welcome-shadow: 0 30px 100px rgba(0, 0, 0, 0.45);
            --welcome-primary-bg: #ffffff;
            --welcome-primary-text: #111111;
            --welcome-ghost-text: #ffffff;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top, var(--welcome-bg-top), transparent 30%),
                linear-gradient(180deg, var(--welcome-bg-main-1), var(--welcome-bg-main-2) 45%, var(--welcome-bg-main-3));
            color: var(--welcome-text);
        }

        .wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .panel {
            width: min(100%, 960px);
            display: grid;
            gap: 0;
            grid-template-columns: 1.1fr 0.9fr;
            border: 1px solid var(--welcome-border);
            border-radius: 28px;
            overflow: hidden;
            background: var(--welcome-panel);
            box-shadow: var(--welcome-shadow);
            backdrop-filter: blur(14px);
        }

        .hero {
            padding: 44px;
            border-right: 1px solid var(--welcome-border);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border: 1px solid var(--welcome-border);
            border-radius: 9999px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--welcome-muted);
        }

        h1 {
            margin: 24px 0 16px;
            font-size: clamp(2.5rem, 5vw, 4.8rem);
            line-height: 0.95;
            letter-spacing: -0.04em;
        }

        p {
            margin: 0;
            color: var(--welcome-muted);
            line-height: 1.7;
            max-width: 56ch;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 14px 20px;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.78rem;
            border: 1px solid var(--welcome-border);
        }

        .button.primary {
            background: var(--welcome-primary-bg);
            color: var(--welcome-primary-text);
        }

        .button.ghost {
            color: var(--welcome-ghost-text);
            background: transparent;
        }

        .theme-toggle {
            position: fixed;
            top: 16px;
            right: 16px;
            border-radius: 9999px;
            border: 1px solid var(--welcome-border);
            background: var(--welcome-panel);
            color: var(--welcome-text);
            padding: 10px 14px;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            cursor: pointer;
            z-index: 10;
        }

        .sidebar {
            padding: 44px;
            display: grid;
            gap: 16px;
            align-content: start;
            background: var(--welcome-panel-alt);
        }

        .metric {
            border: 1px solid var(--welcome-border);
            border-radius: 22px;
            padding: 18px;
            background: var(--welcome-panel-alt);
        }

        .metric strong {
            display: block;
            font-size: 1.4rem;
            color: var(--welcome-text);
            margin-bottom: 4px;
        }

        @media (max-width: 860px) {
            .panel {
                grid-template-columns: 1fr;
            }

            .hero {
                border-right: 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }
        }
    </style>
</head>
<body>
    <button type="button" class="theme-toggle" onclick="toggleTheme()">Ganti tema</button>
    <div class="wrap">
        <div class="panel">
            <section class="hero">
                <div class="eyebrow">Inventory Management System</div>
                <h1>CV Haathee</h1>
                <p>
                </p>
                <div class="actions">
                    <a href="{{ route('login') }}" class="button primary">Masuk</a>
                    <a href="{{ route('register') }}" class="button ghost">Daftar</a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
