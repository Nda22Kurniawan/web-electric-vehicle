@extends('layouts.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . Auth::user()->name . '!')

@section('content')
    <!-- Dashboard Stats -->
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-primary-light">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title">Booking Aktif</h5>
                    <h3 class="card-value">3</h3>
                    <span class="card-change change-up"><i class="fas fa-arrow-up"></i> 1 dari bulan lalu</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-secondary-light">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h5 class="card-title">Kendaraan</h5>
                    <h3 class="card-value">2</h3>
                    <span class="card-change"><i class="fas fa-equals"></i> sama dengan bulan lalu</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-success-light">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title">Servis Selesai</h5>
                    <h3 class="card-value">12</h3>
                    <span class="card-change change-up"><i class="fas fa-arrow-up"></i> 3 dari bulan lalu</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-warning-light">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="card-title">Servis Berikutnya</h5>
                    <h3 class="card-value">20 Mei</h3>
                    <span class="card-change change-down"><i class="fas fa-arrow-down"></i> 2 hari lagi</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking Chart -->
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="mb-4">Statistik Booking Servis</h5>
                <canvas id="bookingChart" height="250"></canvas>
            </div>
        </div>

        <!-- Vehicle Status -->
        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="mb-4">Status Kendaraan</h5>
                <canvas id="vehicleChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Booking Terakhir</h5>
                    <a href="" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Tanggal</th>
                                <th>Kendaraan</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#EM-2023-065</td>
                                <td>10 Jun 2023</td>
                                <td>Viar Q1</td>
                                <td>Servis Rutin</td>
                                <td><span class="booking-status status-confirmed">Dikonfirmasi</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">Detail</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#EM-2023-064</td>
                                <td>5 Jun 2023</td>
                                <td>Gesits G1</td>
                                <td>Ganti Baterai</td>
                                <td><span class="booking-status status-completed">Selesai</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">Detail</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#EM-2023-063</td>
                                <td>28 Mei 2023</td>
                                <td>Viar Q1</td>
                                <td>Perbaikan Motor</td>
                                <td><span class="booking-status status-completed">Selesai</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">Detail</button>
                                </td>
                            </tr>
                            <tr>
                                <td>#EM-2023-062</td>
                                <td>15 Mei 2023</td>
                                <td>Gesits G1</td>
                                <td>Upgrade Performa</td>
                                <td><span class="booking-status status-completed">Selesai</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">Detail</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Booking Chart
        const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        const bookingChart = new Chart(bookingCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Booking Servis',
                    data: [5, 8, 6, 9, 7, 10, 8, 11, 9, 12, 10, 13],
                    backgroundColor: 'rgba(46, 91, 255, 0.1)',
                    borderColor: '#2e5bff',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
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

        // Vehicle Chart
        const vehicleCtx = document.getElementById('vehicleChart').getContext('2d');
        const vehicleChart = new Chart(vehicleCtx, {
            type: 'doughnut',
            data: {
                labels: ['Servis Rutin', 'Ganti Baterai', 'Perbaikan Motor', 'Lainnya'],
                datasets: [{
                    data: [35, 25, 20, 20],
                    backgroundColor: [
                        '#2e5bff',
                        '#ff6b35',
                        '#28a745',
                        '#6c757d'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection