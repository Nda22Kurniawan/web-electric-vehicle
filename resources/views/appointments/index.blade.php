<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Servis - SantosoEV</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" rel="stylesheet">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .appointments-menu-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            border: none;
            background-color: white;
        }

        .appointments-menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .appointments-menu-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            height: 100px;
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .appointments-menu-card:hover .appointments-menu-icon {
            transform: scale(1.2);
        }

        .appointments-menu-title {
            text-align: center;
            font-weight: 600;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .appointments-menu-description {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 0 1rem;
            color: var(--gray);
        }

        .filter-card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            border: none;
            background-color: white;
            padding: 1.5rem;
        }

        .status-badge {
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

        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .appointment-list-item {
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .appointment-list-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .appointment-date {
            font-weight: bold;
            color: var(--primary);
        }

        .appointment-customer {
            font-weight: 600;
        }

        .appointment-vehicle {
            color: var(--gray);
        }

        .appointment-actions a {
            margin-right: 0.5rem;
        }

        .action-button {
            width: 100%;
            border-radius: 10px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 600;
        }

        .action-button:hover {
            transform: translateY(-3px);
        }

        .appointments-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .appointments-stat-card {
            flex: 1;
            padding: 1.5rem;
            border-radius: 15px;
            margin-right: 1rem;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .appointments-stat-card:last-child {
            margin-right: 0;
        }

        .appointments-stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .appointments-stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .appointments-stats {
                flex-direction: column;
            }

            .appointments-stat-card {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    @extends('layouts.layout')

    @section('title', 'Jadwal Servis')

    @section('page-title', 'Jadwal Servis')
    @section('page-subtitle', 'Kelola semua jadwal servis kendaraan')

    @section('content')
    <div class="container-fluid">
        <!-- Statistics -->
        <div class="appointments-stats">
            <div class="appointments-stat-card">
                <div class="appointments-stat-value text-primary">{{ $today_count ?? 0 }}</div>
                <div class="appointments-stat-label">Servis Hari Ini</div>
            </div>
            <div class="appointments-stat-card">
                <div class="appointments-stat-value text-warning">{{ $pending_count ?? 0 }}</div>
                <div class="appointments-stat-label">Menunggu Konfirmasi</div>
            </div>
            <div class="appointments-stat-card">
                <div class="appointments-stat-value text-info">{{ $in_progress_count ?? 0 }}</div>
                <div class="appointments-stat-label">Sedang Diproses</div>
            </div>
            <div class="appointments-stat-card">
                <div class="appointments-stat-value text-success">{{ $completed_count ?? 0 }}</div>
                <div class="appointments-stat-label">Selesai Bulan Ini</div>
            </div>
        </div>

        <!-- Menu Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="appointments-menu-card">
                    <div class="card-body">
                        <div class="appointments-menu-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <h5 class="appointments-menu-title">Servis Hari Ini</h5>
                        <p class="appointments-menu-description">Lihat dan kelola daftar servis yang dijadwalkan untuk hari ini.</p>
                        <a href="{{ route('appointments.today') }}" class="btn btn-primary action-button">Buka</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="appointments-menu-card">
                    <div class="card-body">
                        <div class="appointments-menu-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h5 class="appointments-menu-title">Buat Jadwal Baru</h5>
                        <p class="appointments-menu-description">Tambahkan jadwal servis baru untuk pelanggan.</p>
                        <a href="{{ route('appointments.create') }}" class="btn btn-success action-button">Tambah</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="appointments-menu-card">
                    <div class="card-body">
                        <div class="appointments-menu-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <h5 class="appointments-menu-title">Semua Jadwal</h5>
                        <p class="appointments-menu-description">Lihat dan kelola semua jadwal servis.</p>
                        <a href="{{ route('appointments.index') }}" class="btn btn-info action-button">Lihat</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="appointments-menu-card">
                    <div class="card-body">
                        <div class="appointments-menu-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="appointments-menu-title">Lacak Jadwal</h5>
                        <p class="appointments-menu-description">Lacak status servis kendaraan dengan kode tracking.</p>
                        <a href="{{ route('appointments.track') }}" class="btn btn-secondary action-button">Lacak</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Recent Appointments -->
        <div class="row mt-4">
            <!-- Filters -->
            <div class="col-md-4">
                <div class="filter-card">
                    <h5 class="mb-4">Filter Jadwal</h5>
                    <form action="{{ route('appointments.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="date_range" class="form-label">Rentang Tanggal</label>
                            <input type="text" class="form-control flatpickr-range" id="date_range" name="date_range" placeholder="Pilih rentang tanggal">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu Konfirmasi</option>
                                <option value="confirmed">Terkonfirmasi</option>
                                <option value="in_progress">Sedang Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Jenis Kendaraan</label>
                            <select class="form-select" id="vehicle_type" name="vehicle_type">
                                <option value="">Semua Jenis</option>
                                <!-- Loop through vehicle types here -->
                                <option value="Motor">Motor</option>
                                <option value="Mobil">Mobil</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                    </form>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Jadwal Servis Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <!-- Appointment list -->
                        <div class="appointment-list">
                            @forelse ($recent_appointments ?? [] as $appointment)
                            <div class="appointment-list-item p-3 border">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="appointment-date">
                                            {{ $appointment->appointment_date->format('d M Y') }}
                                        </div>
                                        <div class="appointment-time">
                                            {{ $appointment->appointment_time->format('H:i') }} WIB
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="appointment-customer">
                                            {{ $appointment->customer_name }}
                                        </div>
                                        <div class="appointment-vehicle">
                                            {{ $appointment->vehicle->brand }} {{ $appointment->vehicle->model }}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="status-badge status-{{ $appointment->status }}">
                                            @switch($appointment->status)
                                                @case('pending')
                                                    Menunggu
                                                    @break
                                                @case('confirmed')
                                                    Terkonfirmasi
                                                    @break
                                                @case('in_progress')
                                                    Diproses
                                                    @break
                                                @case('completed')
                                                    Selesai
                                                    @break
                                                @case('cancelled')
                                                    Dibatalkan
                                                    @break
                                                @default
                                                    {{ $appointment->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <img src="/api/placeholder/150/150" alt="No Appointments" class="mb-3">
                                <h6>Belum ada jadwal servis</h6>
                                <p class="text-muted">Jadwalkan servis baru dengan mengklik tombol "Buat Jadwal Baru"</p>
                                <a href="{{ route('appointments.create') }}" class="btn btn-primary">Buat Jadwal Baru</a>
                            </div>
                            @endforelse
                        </div>

                        @if(!empty($recent_appointments) && count($recent_appointments) > 0)
                        <div class="text-center mt-4">
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">Lihat Semua Jadwal</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr for date range
            flatpickr('.flatpickr-range', {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
            });
        });
    </script>
    @endsection
</body>

</html>