@extends('layouts.layout')

@section('title', 'Dashboard Mekanik')

@section('page-title', 'Dashboard Mekanik')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('content')
<div class="container-fluid">
    <!-- Statistic Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="card-icon bg-primary-light">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="card-title">Work Order Hari Ini</div>
                    <div class="card-value">{{ $todayWorkOrders ?? 0 }}</div>
                    <div class="card-change">
                        <i class="fas fa-arrow-up"></i>
                        <span class="change-up">5% dari minggu lalu</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="card-icon bg-secondary-light">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="card-title">Jadwal Hari Ini</div>
                    <div class="card-value">{{ $todayAppointments ?? 0 }}</div>
                    <div class="card-change">
                        <i class="fas fa-arrow-up"></i>
                        <span class="change-up">12% dari minggu lalu</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="card-icon bg-success-light">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-title">Servis Selesai</div>
                    <div class="card-value">{{ $completedServices ?? 0 }}</div>
                    <div class="card-change">
                        <i class="fas fa-arrow-up"></i>
                        <span class="change-up">8% dari minggu lalu</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="card-icon bg-warning-light">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-title">Dalam Proses</div>
                    <div class="card-value">{{ $inProgressServices ?? 0 }}</div>
                    <div class="card-change">
                        <i class="fas fa-arrow-down"></i>
                        <span class="change-down">3% dari minggu lalu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Work Orders -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Work Order Terbaru</h5>
                        <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. WO</th>
                                    <th>Kendaraan</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($workOrders) && count($workOrders) > 0)
                                    @foreach($workOrders as $workOrder)
                                    <tr>
                                        <td>{{ $workOrder->work_order_number }}</td>
                                        <td>{{ $workOrder->vehicle->license_plate }} - {{ $workOrder->vehicle->model }}</td>
                                        <td>{{ $workOrder->customer->name }}</td>
                                        <td>
                                            @if($workOrder->status == 'pending')
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                            @elseif($workOrder->status == 'in_progress')
                                            <span class="badge bg-info">Dalam Proses</span>
                                            @elseif($workOrder->status == 'completed')
                                            <span class="badge bg-success">Selesai</span>
                                            @else
                                            <span class="badge bg-secondary">{{ $workOrder->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('work-orders.show', $workOrder->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data work order</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Jadwal Hari Ini</h5>
                </div>
                <div class="card-body">
                    @if(isset($todaySchedules) && count($todaySchedules) > 0)
                        @foreach($todaySchedules as $schedule)
                        <div class="booking-card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</h6>
                                    <span class="booking-status status-{{ strtolower($schedule->status) }}">{{ $schedule->status }}</span>
                                </div>
                                <p class="mb-1"><strong>Pelanggan:</strong> {{ $schedule->appointment->customer->name }}</p>
                                <p class="mb-1"><strong>Kendaraan:</strong> {{ $schedule->appointment->vehicle->license_plate }}</p>
                                <p class="mb-1"><strong>Jenis Servis:</strong> {{ $schedule->appointment->service_type }}</p>
                                <div class="d-flex justify-content-end mt-2">
                                    <a href="{{ route('service-schedules.show', $schedule->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                            <p>Tidak ada jadwal untuk hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Performance -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h5>Performa Servis Bulan Ini</h5>
                <canvas id="servicePerformanceChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Service Performance Chart
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('servicePerformanceChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1 Mei', '5 Mei', '10 Mei', '15 Mei', '20 Mei', '25 Mei', '30 Mei'],
                datasets: [{
                    label: 'Work Order Selesai',
                    data: [5, 8, 12, 7, 10, 15, 18],
                    backgroundColor: 'rgba(46, 91, 255, 0.1)',
                    borderColor: '#2e5bff',
                    borderWidth: 2,
                    tension: 0.4
                }, {
                    label: 'Rata-rata Waktu Pengerjaan (jam)',
                    data: [3, 2.5, 3.2, 2.8, 2, 2.2, 1.8],
                    backgroundColor: 'rgba(255, 107, 53, 0.1)',
                    borderColor: '#ff6b35',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection