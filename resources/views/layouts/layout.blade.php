<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SantosoEV</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- AOS Animate -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <style>
        :root {
            --primary: #2e5bff;
            --secondary: #ff6b35;
            --dark: #252525;
            --light: #f8f9fa;
            --gray: #6c757d;
            --sidebar: #1a2238;
            --sidebar-hover: #2e5bff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
            background-color: #f5f7fa;
            overflow-x: hidden;
        }

        .main-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            /* Sesuai dengan lebar sidebar */
            background-color: white;
            z-index: 999;
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        /* Padding untuk konten utama agar tidak tertutup header */
        .content-wrapper {
            margin-top: 150px;
            /* Sesuaikan dengan tinggi header */
            padding: 0 1.5rem 1.5rem;
        }

        /* Sidebar */
        .sidebar {
            background-color: var(--sidebar);
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-brand a {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
        }

        .sidebar-brand span {
            color: var(--secondary);
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        /* Custom scrollbar styling */
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(46, 91, 255, 0.2);
            border-left-color: var(--primary);
        }

        .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
            object-fit: cover;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .page-title h2 {
            font-weight: 700;
            margin-bottom: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
        }

        .notification-icon {
            position: relative;
            margin-right: 1.5rem;
            font-size: 1.25rem;
            color: var(--gray);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 18px;
            height: 18px;
            background-color: var(--secondary);
            color: white;
            border-radius: 50%;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-dropdown img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        /* Dashboard Cards */
        .dashboard-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border: none;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .bg-primary-light {
            background-color: rgba(46, 91, 255, 0.1);
            color: var(--primary);
        }

        .bg-secondary-light {
            background-color: rgba(255, 107, 53, 0.1);
            color: var(--secondary);
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .card-title {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .card-change {
            font-size: 0.8rem;
        }

        .change-up {
            color: #28a745;
        }

        .change-down {
            color: #dc3545;
        }

        /* Recent Bookings */
        .booking-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            border: none;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .booking-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Chart Container */
        .chart-container {
            background-color: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-header {
                left: 0;
            }

            .sidebar {
                margin-left: -250px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: block !important;
            }
        }

        @media (min-width: 992px) {
            .toggle-sidebar {
                display: none !important;
            }
        }

        /* Logout button styling */
        .logout-button {
            background: none;
            border: none;
            color: #212529;
            text-align: left;
            padding: 0.25rem 1rem;
            display: flex;
            align-items: center;
            width: 100%;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #f8f9fa;
        }

        .logout-button i {
            margin-right: 0.5rem;
        }

        @yield('additional-styles')
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">Santoso<span>EV</span></a>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Routes for all users -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('appointments*') ? 'active' : '' }}"
                        href="{{ route('appointments.index') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Jadwal Servis</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('vehicles*') ? 'active' : '' }}"
                        href="{{ route('vehicles.index') }}">
                        <i class="fas fa-motorcycle"></i>
                        <span>Kendaraan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('services*') ? 'active' : '' }}"
                        href="{{ route('services.index') }}">
                        <i class="fas fa-tools"></i>
                        <span>Layanan Servis</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('payments*') ? 'active' : '' }}"
                        href="{{ route('payments.index') }}">
                        <i class="fas fa-credit-card"></i>
                        <span>Pembayaran</span>
                    </a>
                </li>

                @if(auth()->user()->isAdmin())
                    <!-- Admin Only Routes -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('parts*') ? 'active' : '' }}" href="{{ route('parts.index') }}">
                            <i class="fas fa-cogs"></i>
                            <span>Inventaris Spare Part</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('service-categories*') ? 'active' : '' }}"
                            href="{{ route('service-categories.index') }}">
                            <i class="fas fa-list"></i>
                            <span>Kategori Servis</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('inventory-transactions*') ? 'active' : '' }}"
                            href="{{ route('inventory-transactions.index') }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transaksi Inventaris</span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->isMechanic())
                    <!-- Mechanic Only Routes -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('work-orders*') ? 'active' : '' }}"
                            href="{{ route('work-orders.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Work Order</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('service-schedules*') ? 'active' : '' }}"
                            href="{{ route('service-schedules.index') }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Jadwal Mekanik</span>
                        </a>
                    </li>
                @endif

                <!-- Common supporting routes -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('customer-feedback*') ? 'active' : '' }}"
                        href="{{ route('customer-feedback.index') }}">
                        <i class="fas fa-comment"></i>
                        <span>Feedback Pelanggan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('notifications*') ? 'active' : '' }}"
                        href="{{ route('notifications.index') }}">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('settings*') ? 'active' : '' }}"
                        href="{{ route('settings.index') }}">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <img src="/api/placeholder/80/80" alt="User" class="user-avatar">
                <div>
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <small class="text-muted">{{ Auth::user()->role }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <div class="main-header">
            <div class="header">
                <div class="page-title">
                    <h2>@yield('page-title')</h2>
                    <small class="text-muted">@yield('page-subtitle')</small>
                </div>

                <div class="user-menu">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>

                    <div class="dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/api/placeholder/80/80" alt="User" class="me-2">
                            <span>{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i
                                        class="fas fa-user me-2"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i
                                        class="fas fa-cog me-2"></i>Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="logout-button">
                                        <i class="fas fa-sign-out-alt"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <button class="btn btn-outline-secondary ms-3 toggle-sidebar" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animate -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- Custom JS -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('main-content').classList.toggle('active');
        });
    </script>

    @yield('scripts')
</body>

</html>