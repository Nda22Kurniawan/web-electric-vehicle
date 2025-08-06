@extends('layouts.layout')

@section('title', 'Detail Pengguna')

@section('page-title', 'Detail Pengguna')
@section('page-subtitle', 'Informasi lengkap pengguna')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- User Profile Card -->
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Profil Pengguna
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <div class="mb-2">
                            @if($user->role === 'admin')
                                <span class="badge bg-success fs-6">{{ ucfirst($user->role) }}</span>
                            @elseif($user->role === 'mechanic')
                                <span class="badge bg-info fs-6">{{ ucfirst($user->role) }}</span>
                            @else
                                <span class="badge bg-warning fs-6">{{ ucfirst($user->role) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row text-start">
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Email:</strong></p>
                            <p class="text-muted mb-3">{{ $user->email }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Telepon:</strong></p>
                            <p class="text-muted mb-3">{{ $user->phone }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Bergabung:</strong></p>
                            <p class="text-muted mb-3">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Terakhir Update:</strong></p>
                            <p class="text-muted mb-3">{{ $user->updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 col-lg-7">
            @if($user->isCustomer())
                <!-- Customer Data -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-white-50">Total Kendaraan</div>
                                        <div class="h4">{{ $user->vehicles->count() }}</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-car fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-white-50">Total Appointment</div>
                                        <div class="h4">{{ $user->appointments->count() }}</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-white-50">Work Orders</div>
                                        <div class="h4">{{ $user->workOrders->count() }}</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-wrench fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicles List -->
                @if($user->vehicles->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-car me-1"></i>
                        Daftar Kendaraan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Merek</th>
                                        <th>Model</th>
                                        <th>Tahun</th>
                                        <th>Plat Nomor</th>
                                        <th>Terdaftar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->brand }}</td>
                                        <td>{{ $vehicle->model }}</td>
                                        <td>{{ $vehicle->year }}</td>
                                        <td><strong>{{ $vehicle->license_plate }}</strong></td>
                                        <td>{{ $vehicle->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Appointments -->
                @if($user->appointments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Appointment Terbaru
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Kendaraan</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->appointments->take(5) as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                                        <td>{{ $appointment->appointment_time }}</td>
                                        <td>{{ $appointment->vehicle->license_plate ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'confirmed' ? 'info' : 'warning') }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($appointment->notes, 30) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

            @elseif($user->isMechanic())
                <!-- Mechanic Data -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-white-50">Total Work Orders</div>
                                        <div class="h4">{{ $user->mechanicWorkOrders->count() }}</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-tools fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-white-50">Completed</div>
                                        <div class="h4">{{ $user->mechanicWorkOrders->where('status', 'completed')->count() }}</div>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Work Orders -->
                @if($user->mechanicWorkOrders->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-wrench me-1"></i>
                        Work Orders Terbaru
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Customer</th>
                                        <th>Kendaraan</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->mechanicWorkOrders->take(5) as $workOrder)
                                    <tr>
                                        <td>{{ $workOrder->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $workOrder->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $workOrder->vehicle->license_plate ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $workOrder->status === 'completed' ? 'success' : ($workOrder->status === 'in_progress' ? 'info' : 'warning') }}">
                                                {{ ucfirst($workOrder->status) }}
                                            </span>
                                        </td>
                                        <td>Rp {{ number_format($workOrder->total_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

            @else
                <!-- Admin Data -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-shield me-1"></i>
                        Informasi Administrator
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Pengguna ini memiliki hak akses administrator dengan kontrol penuh terhadap sistem.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Hak Akses:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i> Mengelola semua pengguna</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Mengakses semua data</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Konfigurasi sistem</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Laporan dan analitik</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Tanggung Jawab:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-shield-alt text-primary me-2"></i> Keamanan sistem</li>
                                    <li><i class="fas fa-database text-primary me-2"></i> Backup data</li>
                                    <li><i class="fas fa-users-cog text-primary me-2"></i> Manajemen pengguna</li>
                                    <li><i class="fas fa-chart-line text-primary me-2"></i> Monitoring performa</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection