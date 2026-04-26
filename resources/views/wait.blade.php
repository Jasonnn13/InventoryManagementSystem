<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting for Approval</title>
    <style>
        :root {
            color-scheme: dark;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at top, rgba(255, 255, 255, 0.08), transparent 40%),
                linear-gradient(180deg, #0a0a0a, #171717 50%, #0f0f0f);
            color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .card {
            width: min(92vw, 440px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;
            background: rgba(18, 18, 18, 0.88);
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.45);
            padding: 32px;
            text-align: center;
            backdrop-filter: blur(12px);
        }

        .spinner {
            width: 56px;
            height: 56px;
            margin: 0 auto 20px;
            border-radius: 9999px;
            border: 4px solid rgba(255, 255, 255, 0.16);
            border-top-color: #ffffff;
            animation: spin 1s linear infinite;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 1.5rem;
        }

        p {
            margin: 0;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.6;
        }

        .meta {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
            align-items: center;
            font-size: 0.9rem;
        }

        .logout-button {
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: #fff;
            color: #111;
            border-radius: 9999px;
            padding: 10px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h1>Menunggu verifikasi admin</h1>
        <p>Akun Anda aktif, tetapi akses masih menunggu persetujuan.</p>
        <div class="meta">
            <span>{{ Auth::user()->name }}</span>
            <button class="logout-button" onclick="document.getElementById('logout-form').submit();">Keluar</button>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</body>
</html>
