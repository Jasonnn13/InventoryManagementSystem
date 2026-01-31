<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            overflow-y: auto;
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
            margin-bottom: 20px;
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
        .btn-secondary.red-outline {
            border: 1px solid #000; /* Black outline for delete button */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
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
            margin-bottom: 20px;
        }
        .category-header h3 {
            margin: 0;
        }
        .overdue {
            background-color: #ff4444; /* Red background for overdue */
        }
        .overdue:hover {
            background-color: #cc0000; /* Darker red on hover for overdue */
        }
        .action-icons {
            display: flex;
            gap: 10px;
        }
        .action-icons i {
            cursor: pointer;
            font-size: 18px;
            color: #fff;
        }
        .action-icons .edit {
            color: #4caf50;
        }
        .action-icons .delete {
            color: #f44336;
        }
        .overdue {
            background-color: #ff4444; /* Red background for overdue */
        }

        .overdue:hover {
            background-color: #cc0000; /* Darker red on hover for overdue */
        }
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
        table {
        width: 100%; /* Make the table full width */
        border-collapse: collapse; /* Ensure borders are collapsed */
        }
        th, td {
            display: block; /* Make table cells block elements */
            width: 100%; /* Full width for cells */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
            padding: 10px; /* Add some padding */
            text-align: left; /* Align text to the left */
        }
        thead {
            display: none; /* Hide the header on small screens */
        }
        tr {
            display: block; /* Make each row a block */
            margin-bottom: 10px; /* Add space between rows */
            border-bottom: 1px solid #5a5a5a; /* Add a border between rows */
            background-color: #2e2e2e; /* Background color for each row */
            border: 1px solid #4caf50; /* Add a green border around each cell */
            gap: 20px; /* Add some space between cells */
        }
        tr:last-child {
            border-bottom: none; /* Remove border from the last row */
        }
        td::before {
            content: attr(data-label); /* Add labels for each cell */
            font-weight: bold;
            display: block;
            margin-bottom: 5px; /* Space between label and data */
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
                    <li><a href="/pembelian" class="active">Pembelian</a></li>
                    <li><a href="/penjualan">Penjualan</a></li>
                    <li><a href="/suppliers">Suppliers</a></li>
                    <li><a href="/customers">Customers</a></li>
                </ul>
            </nav>
        </aside>
        <div class="backdrop" id="backdrop" onclick="toggleSidebar()"></div>
        <main class="main-content">
            <header class="header">
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
                <h1>Pembelian</h1>
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <button class="logout-button" onclick="document.getElementById('logout-form').submit();">Logout</button>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </header>
            <section class="content">
                <div class="search-form">
                    <form action="{{ route('pembelian.index') }}" method="GET">
                        <input type="text" name="search" placeholder="Search by Name..." value="{{ $search }}">
                        <button type="submit">Search</button>
                    </form>
                </div>
                <div class="category-header">
                    <h3>List Pembelian</h3>
                    <a href="{{ route('pembelian.create') }}" class="btn btn-primary">Add Pembelian</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Gudang</th>
                            <th>Total</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelians as $item)
                        <tr data-created-by="{{ $item->user->id }}" onclick="window.location='{{ route('rincianpembelian.index', ['pembelian_id' => $item->id]) }}'">                                
                                <td data-label="Name">{{ $item->supplier->name }}</td>
                                <td data-label="Gudang">{{ $item->gudang->name }}</td>
                                <td data-label="Total">Rp. {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td data-label="Created By">{{ $item->user->name }}</td>
                                <td data-label="Created At">{{ $item->created_at->format('d-m-Y') }}</td>
                                <td data-label="Actions">
                                    <div class="action-icons">
                                        <a href="{{ route('pembelian.edit', $item->id) }}" class="edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" style="background: none; border: none; cursor: pointer;" onclick="confirmation(event)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
    @if ($pembelians->hasPages())
        <ul style="display: flex; list-style-type: none; padding: 0; margin: 0;">
            {{-- Previous Page Link --}}
            @if ($pembelians->onFirstPage())
                <li style="display: inline-block; margin: 0 15px; color: #888; cursor: not-allowed;"><span>&laquo;</span></li>
            @else
                <li style="display: inline-block; margin: 0 15px;">
                    <a href="{{ $pembelians->previousPageUrl() }}" style="color: white; text-decoration: none; font-size: 18px; transition: color 0.3s ease;">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($pembelians->appends(['search' => $search])->links()->elements[0] as $page => $url)
                @if ($page == $pembelians->currentPage())
                    <li style="display: inline-block; margin: 0 15px; font-weight: bold; color: azure;">
                        <span>{{ $page }}</span>
                    </li>
                @else
                    <li style="display: inline-block; margin: 0 15px;">
                        <a href="{{ $url }}" style="color: white; text-decoration: none; font-size: 18px; transition: color 0.3s ease;">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($pembelians->hasMorePages())
                <li style="display: inline-block; margin: 0 15px;">
                    <a href="{{ $pembelians->nextPageUrl() }}" style="color: white; text-decoration: none; font-size: 18px; transition: color 0.3s ease;">&raquo;</a>
                </li>
            @else
                <li style="display: inline-block; margin: 0 15px; color: #888; cursor: not-allowed;"><span>&raquo;</span></li>
            @endif
        </ul>
    @endif
</div>
            </section>
        </main>
    </div>

    <script>
    let userLevel = {{ Auth::user()->level }}; // User's level (1 or 2)
    let userId = {{ Auth::user()->id }}; // User's ID

    // Function to toggle sidebar visibility
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.getElementById('backdrop');
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
    }

    // Confirmation dialog for deletion
    function confirmation(event) {
        event.stopPropagation(); // Stop the event from propagating to the <tr> click event
        event.preventDefault();   // Prevent the form from submitting immediately

        swal({
            title: "Are you sure you want to delete this record?",
            text: "This action cannot be undone.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                event.target.closest('form').submit();
            }
        });
    }

    // Disable edit/delete buttons based on user level and ownership
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('tbody tr').forEach(function (row) {
            let createdBy = parseInt(row.getAttribute('data-created-by')); // User ID who created the record
            let editButton = row.querySelector('.edit');
            let deleteButton = row.querySelector('.delete');

            // Check if the user is level 1 and doesn't own this record
            if (userLevel === 1 && userId !== createdBy) {
                // Hide edit and delete buttons for level 1 users who don't own the record
                if (editButton) {
                    editButton.style.display = 'none';
                }
                if (deleteButton) {
                    deleteButton.style.display = 'none';
                }
            }
        });
    });
    </script>
</body>
</html>

</body>
</html>
