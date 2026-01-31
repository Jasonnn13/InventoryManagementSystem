<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
            background-color: #1f1f1f;
        }
        
        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #2c2c2c;
            padding: 20px;
            transition: transform 0.3s ease;
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
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Allow wrapping for smaller screens */
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

        .hamburger {
            display: none; /* Hide by default on larger screens */
            font-size: 1.5em;
            cursor: pointer;
            background: none;
            border: none;
            color: #fff;
        }   

        .content {
            background-color: #3c3c3c;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #4caf50;
            color: #fff;
        }

        .btn-secondary {
            background-color: #f44336;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #5a5a5a;
        }

        th {
            background-color: #4caf50;
        }

        tr:hover {
            background-color: #3a3a3a;
        }

        .action-icons {
            display: flex;
            gap: 10px;
        }

        .action-icons a,
        .action-icons button {
            background: none;
            border: none;
            cursor: pointer;
            color: #fff;
        }

        .action-icons a:hover,
        .action-icons button:hover {
            color: #4caf50;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input {
            margin-top: 20px;
            padding: 10px;
            width: calc(100% - 120px);
            border: 1px solid #4caf50;
            border-radius: 5px 0 0 5px;
            background-color: #3a3a3a;
            color: #fff;
        }

        .search-form button {
            padding: 10px;
            border: none;
            border-radius: 0 5px 5px 0;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .category-header h3 {
            margin: 0;
        }

        .cards-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap; /* Wrap cards for smaller screens */
        }

        .card {
            background-color: #4caf50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 200px;
            position: relative;
        }

        .card-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 1.2em;
            margin-bottom: 50px;
        }

        .card-link {
            color: white;
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            position: absolute;
            bottom: 20px;
            left: 20px;
        }

        .card-link:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 4em;
            opacity: 0.3;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            margin-bottom: 20px;
        }

        .chart-container canvas {
            height: 100% !important;
            width: 100% !important;
        }

        /* Responsive styles for mobile devices */


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

    .user-name {
        display: none; /* Hide user info on small screens */
    }

    .main-content {
        padding: 10px; /* Reduce padding for smaller screens */
        margin-left: 0; /* No left margin on small screens */
    }

    .container {
        flex-direction: column;
    }

    .menu li a {
        padding: 8px;
    }

    .header {
        padding: 10px;
        /* flex-direction: column; */
        align-items: center;
        justify-content: space-between;

    }

    .header h1 {
        font-size: 1.5em;
        align-self: center;
        text-align: center;
    }


    .cards-container {
        flex-direction: column;
        gap: 15px; /* Adjust gap for better spacing */
        justify-content: center;
    }

    .card {
        width: 100%;
        max-width: 300px; /* Set a max-width for better alignment */
        margin: 0 auto; /* Center the card */
    }

    .chart-container {
        height: 200px; /* Reduce chart height for smaller screens */
    }
}

    </style>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
        
    <div class="container">
        <aside class="sidebar">
            <nav class="menu">
                <ul>
                    <li><a href="/dashboard" class="active">Dashboard</a></li>
                    <li><a href="/stocks">Stocks</a></li>
                    <li><a href="/pembelian">Pembelian</a></li>
                    <li><a href="/penjualan">Penjualan</a></li>
                    <li><a href="/suppliers">Suppliers</a></li>
                    <li><a href="/customers">Customers</a></li>
                </ul>
            </nav>
        </aside>
        <div class="backdrop" onclick="toggleSidebar()"></div>
        <main class="main-content">
            <header class="header">
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <button class="logout-button" onclick="document.getElementById('logout-form').submit();">Logout</button>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </header>
            <div class="content">
                <div class="cards-container">
                    <div class="card">
                        <div class="card-number">{{ $pembelianCount }}</div>
                        <div class="card-title">Pembelian Bulan Ini</div>
                        <a href="{{ route('pembelian.index', ['month' => date('m'), 'year' => date('Y')]) }}" class="card-link">View Incoming Goods ➔</a>
                        <div class="card-icon">📦</div>
                    </div>
                    <div class="card">
                        <div class="card-number">{{ $penjualanCount }}</div>
                        <div class="card-title">Penjualan Bulan Ini</div>
                        <a href="{{ route('penjualan.index', ['month' => date('m'), 'year' => date('Y')]) }}" class="card-link">View Outgoing Goods ➔</a>
                        <div class="card-icon">📦</div>
                    </div>
                    <div class="card">
                        <div class="card-number">{{ $countPiutang }}</div>
                        <div class="card-title">Belum Lunas</div>
                        <a href="{{ route('penjualan.index', ['tipe' =>'Piutang', 'status' => 'Belum Lunas']) }}" class="card-link">View ➔</a>
                        <div class="card-icon">📦</div>
                    </div>
                    <div class="card">
                        <div class="card-number">{{$countReq}}</div>
                        <div class="card-title">Perizinan</div>
                        <a href="{{ route('level.index') }}" class="card-link">Atur Perizinan➔</a>
                        <div class="card-icon">🪪</div>
                    </div>


                </div>

                <!-- Add Chart Container -->
                <div class="chart-container" id="profitChartContainer" style="margin-top: 20px;">
                <h3>Grafik Keuntungan (12 Bulan Terakhir)</h3>
                    <canvas id="profitChart"></canvas>
                </div>
                <div class="chart-container" id="penjualanChartContainer" style="margin-top: 20px; padding-top:50px;">
                    <h3>Grafik Penjualan (12 Bulan Terakhir)</h3>
                    <canvas id="penjualanChart"></canvas>
                </div>
                <div class="chart-container" id="pembelianChartContainer" style="margin-top: 20px; padding-top:50px;">
                <h3>Grafik Pembelian (12 Bulan Terakhir)</h3>
                    <canvas id="pembelianChart"></canvas>
                </div>
                
                
            </div>
        </main>
    </div>

    <script>

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.backdrop'); // Fixed selector here
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }


    document.addEventListener('DOMContentLoaded', function() {
        // Add click event listeners to chart containers
        document.getElementById('profitChartContainer').addEventListener('click', function() {
            window.location.href = '/laporan/profit'; // Redirect to the profit report
        });


        // Penjualan Chart
        var ctxPenjualan = document.getElementById('penjualanChart').getContext('2d');
        var penjualanChart = new Chart(ctxPenjualan, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Penjualan',
                    data: @json($penjualanData),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(255, 99, 132, 1)',
                    lineTension: 0.1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                        },
                        grid: {
                            color: '#5a5a5a',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                        },
                        grid: {
                            color: '#5a5a5a',
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#333',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#5a5a5a',
                        borderWidth: 1
                    }
                }
            }
        });

        // Pembelian Chart
        var ctxPembelian = document.getElementById('pembelianChart').getContext('2d');
        var pembelianChart = new Chart(ctxPembelian, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Pembelian',
                    data: @json($pembelianData),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(54, 162, 235, 1)',
                    lineTension: 0.1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                        },
                        grid: {
                            color: '#5a5a5a',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                        },
                        grid: {
                            color: '#5a5a5a',
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#333',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#5a5a5a',
                        borderWidth: 1
                    }
                }
            }
        });

        // Profit Chart
        var ctxProfit = document.getElementById('profitChart').getContext('2d');
        var profitChart = new Chart(ctxProfit, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Profit',
                    data: @json($profitData),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                    lineTension: 0.1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                        },
                        grid: {
                            color: '#5a5a5a',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                        },
                        grid: {
                            color: '#5a5a5a',
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#333',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#5a5a5a',
                        borderWidth: 1
                    }
                }
            }
        });
    });

    </script>

</body>
</html>
