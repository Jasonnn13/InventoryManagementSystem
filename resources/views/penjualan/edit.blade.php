<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
            background-color: #1f1f1f;
        }
        .hamburger {
            display: none; /* Hide by default on larger screens */
            font-size: 1.5em;
            cursor: pointer;
            background: none;
            border: none;
            color: #fff;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #2c2c2c;
            padding: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100px;
        }
        .menu {
            list-style-type: none;
            padding: 0;
        }
        .menu ul {
            list-style-type: none;
            padding: 0;
        }
        .menu li {
            margin: 10px 0;
        }
        .menu li a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }
        .menu li a:hover,
        .menu li a:focus {
            background-color: #3a3a3a;
        }
        .menu li a.active {
            background-color: #4caf50;
        }
        .menu li ul {
            display: none;
            padding-left: 20px;
        }
        .menu li:hover ul,
        .menu li:focus ul {
            display: block;
        }
        .main-content {
            flex-grow: 1;
            background-color: #2e2e2e;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logout-button {
            background-color: #f44336;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .content {
            background-color: #3c3c3c;
            padding: 20px;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #5a5a5a;
            border-radius: 5px;
            background-color: #2c2c2c;
            color: #fff;
        }
        .form-actions {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #4caf50;
            color: #fff;
        }
        .btn-secondary {
            background-color: #f44336;
            color: #fff;
        }
        /* Mobile-specific styles */
        @media (max-width: 480px) {
            .hamburger {
                display: block; /* Show hamburger on small screens */
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100%;
                z-index: 1000;
                overflow-x: hidden;
                background-color: #2c2c2c;
                padding: 20px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .backdrop {
                display: none; /* Hide by default */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
                z-index: 900; /* Behind sidebar but above content */
                transition: opacity 0.3s ease;
            }

            .backdrop.show {
                display: block;
            }

            .menu-toggle {
                display: block;
                background-color: #4caf50;
                color: #fff;
                padding: 10px;
                cursor: pointer;
                border: none;
                border-radius: 5px;
                margin: 10px;
            }

            .header {
                padding: 10px;
                align-items: center;
                justify-content: space-between;
            }

            .header h1 {
                font-size: 1.5em;
                align-self: center;
                text-align: center;
            }

            .main-content {
                padding: 10px; /* Reduce padding for smaller screens */
                margin-left: 0; /* No left margin on small screens */
            }

            .user-name {
                display: none; /* Hide user-name on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <nav class="menu">
                <ul>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/stocks">Stocks</a></li>
                    <li><a href="/pembelian">Pembelian</a></li>
                    <li><a href="/penjualan" class="active">Penjualan</a></li>
                    <li><a href="/suppliers">Suppliers</a></li>
                    <li><a href="/customers">Customers</a></li>
                </ul>
            </nav>
        </aside>
        <div class="backdrop" id="backdrop" onclick="toggleSidebar()"></div>
        <main class="main-content">
            <header class="header">
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
                <h1>Edit Penjualan</h1>
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <button class="logout-button" onclick="document.getElementById('logout-form').submit();">Logout</button>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </header>
            <section class="content">
                <h2>Edit Penjualan Details</h2>
                <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="customers_id">Customer</label>
                        <select id="customers_id" name="customers_id" required>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customers_id', $penjualan->customers_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Lunas" {{ old('status', $penjualan->status) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="Belum Lunas" {{ old('status', $penjualan->status) == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tipe">Tipe</label>
                        <select id="tipe" name="tipe" required>
                            <option value="Cash" {{ old('tipe', $penjualan->tipe) == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Piutang" {{ old('tipe', $penjualan->tipe) == 'Piutang' ? 'selected' : '' }}>Piutang</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sales">Sales</label>
                        <input value="{{ old('sales', $penjualan->sales) }}" type="text" id="sales" name="sales" required>
                    </div>
                    <div class="form-group">
                        <label for="tenggat_waktu">Tenggat</label>
                        <input type="date" id="tenggat_waktu" name="tenggat_waktu" 
                            value="{{ \Carbon\Carbon::parse(old('tenggat_waktu', $penjualan->tenggat_waktu))->format('Y-m-d') }}" 
                            required>
                    </div>
                    <div class="form-group">
                        <label for="diskon">Diskon (%)</label>
                        <input type="number" id="diskon" name="diskon" min="0" max="100" value="{{ old('diskon', $penjualan->diskon) }}" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('backdrop');
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
    </script>
</body>
</html>
