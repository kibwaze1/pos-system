<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS System - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #212529; }
        .sidebar .nav-link { color: #fff; }
        .sidebar .nav-link:hover { background-color: #0d6efd; }

        /* Toggle button - hidden by default, shown only when sidebar is hidden */
        .toggle-sidebar-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1050;
            background: #212529;
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            display: none;
        }
        .sidebar-hidden .toggle-sidebar-btn {
            display: block;
        }
        .sidebar-hidden .sidebar-col {
            display: none !important;
        }
        .sidebar-hidden .main-content-col {
            width: 100% !important;
            flex: 0 0 auto !important;
            max-width: 100% !important;
        }
        body.sidebar-hidden .main-content-col {
            padding-top: 70px;
        }
        body:not(.sidebar-hidden) .main-content-col {
            padding-top: 0;
        }
    </style>
    <style id="sidebarFlashFix">
        .sidebar-col { display: none !important; }
        .main-content-col { width: 100% !important; flex: 0 0 auto !important; max-width: 100% !important; }
        .main-content-col { padding-top: 70px !important; }
        .toggle-sidebar-btn { display: block !important; }
    </style>
</head>
<body>

<button class="toggle-sidebar-btn" id="toggleSidebarBtn">☰</button>

<div class="container-fluid">
    <div class="row" id="mainRow">
        <!-- Sidebar column -->
        <nav class="col-md-2 d-md-block sidebar p-0 sidebar-col" id="sidebarNav">
            <div class="pt-3">
                <h5 class="text-white text-center">POS System</h5>
                <hr class="text-white">
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="{{ route('pos.index') }}"><i class="fas fa-shopping-cart"></i> POS</a></li>
                    @if(auth()->user()->role->name == 'admin')
                        <!-- Admin sees everything -->
                        <li><a class="nav-link" href="{{ route('products.index') }}"><i class="fas fa-box"></i> Products</a></li>
                        <li><a class="nav-link" href="{{ route('products.barcode.print') }}"><i class="fas fa-barcode"></i> Print Barcodes</a></li>
                        <li><a class="nav-link" href="{{ route('inventory.index') }}"><i class="fas fa-cubes"></i> Inventory</a></li>
                        <li><a class="nav-link" href="{{ route('inventory.add-stock') }}"><i class="fas fa-plus-circle"></i> Add Stock</a></li>
                        <li><a class="nav-link" href="{{ route('inventory.history') }}"><i class="fas fa-history"></i> Stock Logs</a></li>
                        <li><a class="nav-link" href="{{ route('customers.index') }}"><i class="fas fa-users"></i> Customers</a></li>
                        <li><a class="nav-link" href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i> Suppliers</a></li>
                        <li><a class="nav-link" href="{{ route('purchases.index') }}"><i class="fas fa-file-invoice"></i> Purchases</a></li>
                        <li><a class="nav-link" href="{{ route('expenses.index') }}"><i class="fas fa-wallet"></i> Expenses</a></li>
                        <li><a class="nav-link" href="{{ route('users.index') }}"><i class="fas fa-user-cog"></i> Users</a></li>
                        <li><a class="nav-link" href="{{ route('reports.index') }}"><i class="fas fa-chart-line"></i> Reports</a></li>
                        <li><a class="nav-link" href="{{ route('settings.index') }}"><i class="fas fa-cogs"></i> Settings</a></li>
                    @else
                        <!-- Cashier – check permissions -->
                        @if(auth()->user()->hasPermission('inventory_view'))
                            <li><a class="nav-link" href="{{ route('inventory.index') }}"><i class="fas fa-cubes"></i> Inventory</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('products'))
                            <li><a class="nav-link" href="{{ route('products.index') }}"><i class="fas fa-box"></i> Products</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('print_barcodes'))
                            <li><a class="nav-link" href="{{ route('products.barcode.print') }}"><i class="fas fa-barcode"></i> Print Barcodes</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('add_stock'))
                            <li><a class="nav-link" href="{{ route('inventory.add-stock') }}"><i class="fas fa-plus-circle"></i> Add Stock</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('inventory_logs'))
                            <li><a class="nav-link" href="{{ route('inventory.history') }}"><i class="fas fa-history"></i> Stock Logs</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('customers'))
                            <li><a class="nav-link" href="{{ route('customers.index') }}"><i class="fas fa-users"></i> Customers</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('suppliers'))
                            <li><a class="nav-link" href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i> Suppliers</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('purchases'))
                            <li><a class="nav-link" href="{{ route('purchases.index') }}"><i class="fas fa-file-invoice"></i> Purchases</a></li>
                        @endif
                        @if(auth()->user()->hasPermission('expenses'))
                            <li><a class="nav-link" href="{{ route('expenses.index') }}"><i class="fas fa-wallet"></i> Expenses</a></li>
                        @endif
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-white"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content column -->
        <main class="col-md-10 ms-sm-auto px-md-4 main-content-col" id="mainContent">
            <div class="pt-3 pb-2 mb-3 border-bottom">
                <h1>@yield('title')</h1>
            </div>
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @yield('content')
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    (function() {
        const isHidden = localStorage.getItem('sidebarHidden') === 'true';
        const flashFix = document.getElementById('sidebarFlashFix');
        if (flashFix) flashFix.remove();
        if (isHidden) {
            document.body.classList.add('sidebar-hidden');
        }
    })();

    const toggleBtn = document.getElementById('toggleSidebarBtn');
    function setSidebarHidden(hidden) {
        if (hidden) {
            document.body.classList.add('sidebar-hidden');
            localStorage.setItem('sidebarHidden', 'true');
        } else {
            document.body.classList.remove('sidebar-hidden');
            localStorage.setItem('sidebarHidden', 'false');
        }
    }
    toggleBtn.addEventListener('click', function() {
        const currentlyHidden = document.body.classList.contains('sidebar-hidden');
        setSidebarHidden(!currentlyHidden);
    });
    const sidebarLinks = document.querySelectorAll('#sidebarNav .nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            localStorage.setItem('sidebarHidden', 'true');
        });
    });
</script>
@stack('scripts')
</body>
</html>
