@extends('layouts.layout')

@section('title', 'Dashboard Customer')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . Auth::user()->name . '!')

@section('content')
    <!-- Customer Stats -->
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-primary-light">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h5 class="card-title">Booking Aktif</h5>
                    <h3 class="card-value">{{ $activeBookings ?? 2 }}</h3>
                    <span class="card-change">
                        <i class="fas fa-info-circle"></i> Menunggu konfirmasi
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-success-light">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h5 class="card-title">Kendaraan Saya</h5>
                    <h3 class="card-value">{{ $totalVehicles ?? 2 }}</h3>
                    <span class="card-change">
                        <i class="fas fa-wrench"></i> Terdaftar dalam sistem
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-info-light">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title">Servis Selesai</h5>
                    <h3 class="card-value">{{ $completedServices ?? 8 }}</h3>
                    <span class="card-change change-up">
                        <i class="fas fa-arrow-up"></i> 2 bulan terakhir
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-warning-light">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5 class="card-title">Servis Berikutnya</h5>
                    <h3 class="card-value">{{ $nextService ?? '25 Jul' }}</h3>
                    <span class="card-change change-down">
                        <i class="fas fa-clock"></i> 6 hari lagi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="mb-4">Aksi Cepat</h5>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary btn-lg w-100 quick-action-btn">
                            <i class="fas fa-plus-circle mb-2"></i>
                            <br>Booking Servis Baru
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('customer.vehicles') }}" class="btn btn-success btn-lg w-100 quick-action-btn">
                            <i class="fas fa-motorcycle mb-2"></i>
                            <br>Kelola Kendaraan
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('customer.bookings.history') }}" class="btn btn-info btn-lg w-100 quick-action-btn">
                            <i class="fas fa-history mb-2"></i>
                            <br>Riwayat Servis
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('customer.profile') }}" class="btn btn-secondary btn-lg w-100 quick-action-btn">
                            <i class="fas fa-user-cog mb-2"></i>
                            <br>Pengaturan Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Service History Chart -->
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="mb-4">Riwayat Servis Saya</h5>
                <canvas id="serviceHistoryChart" height="250"></canvas>
            </div>
        </div>

        <!-- Vehicle Status -->
        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="mb-4">Jenis Layanan Terakhir</h5>
                <canvas id="serviceTypeChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Bookings and Recent Services -->
    <div class="row">
        <!-- Active Bookings -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Booking Aktif</h5>
                    <a href="{{ route('customer.bookings') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>

                @if(isset($activeBookingsList) && count($activeBookingsList) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kendaraan</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeBookingsList as $booking)
                                <tr>
                                    <td>{{ $booking->booking_date }}</td>
                                    <td>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                                    <td>{{ $booking->service_type }}</td>
                                    <td>
                                        <span class="booking-status status-{{ strtolower($booking->status) }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times fa-3x mb-3 text-muted"></i>
                        <p class="text-muted">Tidak ada booking aktif saat ini</p>
                        <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary">Buat Booking Baru</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Services -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Servis Terakhir</h5>
                    <a href="{{ route('customer.bookings.history') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kendaraan</th>
                                <th>Layanan</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15 Jul 2025</td>
                                <td>Viar Q1</td>
                                <td>Servis Rutin</td>
                                <td>Rp 150.000</td>
                            </tr>
                            <tr>
                                <td>28 Jun 2025</td>
                                <td>Gesits G1</td>
                                <td>Ganti Baterai</td>
                                <td>Rp 2.500.000</td>
                            </tr>
                            <tr>
                                <td>10 Jun 2025</td>
                                <td>Viar Q1</td>
                                <td>Perbaikan Motor</td>
                                <td>Rp 750.000</td>
                            </tr>
                            <tr>
                                <td>25 Mei 2025</td>
                                <td>Gesits G1</td>
                                <td>Servis Rutin</td>
                                <td>Rp 200.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Reminder -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="row align-items-center">
                    <div class="col-md-1 text-center">
                        <i class="fas fa-bell fa-2x"></i>
                    </div>
                    <div class="col-md-8">
                        <h6 class="alert-heading mb-1">Pengingat Servis</h6>
                        <p class="mb-0">Kendaraan Viar Q1 Anda sudah waktunya untuk servis rutin. Jarak tempuh saat ini: 4.850 KM</p>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('customer.bookings.create', ['vehicle' => 1]) }}" class="btn btn-primary">
                            Booking Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Service History Chart
        const serviceHistoryCtx = document.getElementById('serviceHistoryChart').getContext('2d');
        const serviceHistoryChart = new Chart(serviceHistoryCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Jumlah Servis',
                    data: [1, 0, 2, 1, 1, 2, 1],
                    backgroundColor: 'rgba(46, 91, 255, 0.8)',
                    borderColor: '#2e5bff',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Service Type Chart
        const serviceTypeCtx = document.getElementById('serviceTypeChart').getContext('2d');
        const serviceTypeChart = new Chart(serviceTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Servis Rutin', 'Ganti Baterai', 'Perbaikan', 'Upgrade'],
                datasets: [{
                    data: [40, 30, 20, 10],
                    backgroundColor: [
                        '#2e5bff',
                        '#28a745',
                        '#ff6b35',
                        '#6f42c1'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .quick-action-btn {
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .quick-action-btn i {
            font-size: 1.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .booking-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d4edda; color: #155724; }
        .status-inprogress { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .card-icon i {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.9);
        }

        .bg-primary-light { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-success-light { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-info-light { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .bg-warning-light { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .bg-secondary-light { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(0,0,0,0.1);
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #2d3748;
        }

        .card-change {
            font-size: 0.875rem;
            color: #718096;
        }

        .change-up { color: #38a169; }
        .change-down { color: #e53e3e; }
    </style>
@endsection