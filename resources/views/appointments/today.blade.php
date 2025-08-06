@extends('layouts.layout')

@section('title', 'Janji Temu Hari Ini')

@section('page-title', 'Janji Temu Hari Ini')
@section('page-subtitle', 'Daftar janji servis untuk tanggal ' . now()->format('d F Y'))

@section('content')
<div class="container-fluid px-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-day me-1"></i>
                    Daftar Janji Hari Ini
                </div>
                <div>
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-plus me-1"></i> Janji Baru
                    </a>
                    <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm me-2">
                        <i class="fas fa-calendar-alt me-1"></i> Semua Janji
                    </a>
                    @if(auth()->user()->role !== 'customer')
<a href="{{ route('service-schedules.index') }}" class="btn btn-secondary btn-sm">
    <i class="fas fa-plus me-1"></i> Tambah Jadwal Layanan
</a>
@endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($appointments->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada janji temu hari ini</h5>
                <p class="text-muted">Anda dapat menambahkan janji baru menggunakan tombol di atas.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover" id="todayAppointmentsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Waktu</th>
                            <th>Servis</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $index => $appointment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $appointment->customer_name }}</strong>
                                <div class="small text-muted">{{ $appointment->customer_phone }}</div>
                                @if($appointment->customer_email)
                                <div class="small text-muted">{{ $appointment->customer_email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($appointment->vehicle)
                                {{ $appointment->vehicle->name }} ({{ $appointment->vehicle->type }})
                                @else
                                <span class="text-muted">Tidak ada data</span>
                                @endif
                            </td>
                            <td>
                                {{ $appointment->appointment_time->format('H:i') }}
                                <div class="small text-muted">
                                    @php
                                        $timeDiff = now()->diffInMinutes($appointment->appointment_time, false);
                                    @endphp
                                    @if($timeDiff > 0)
                                        <span class="text-success">Dalam {{ $timeDiff }} menit</span>
                                    @elseif($timeDiff == 0)
                                        <span class="text-warning">Sekarang</span>
                                    @else
                                        <span class="text-danger">{{ abs($timeDiff) }} menit lalu</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $appointment->service_description }}">
                                    {{ $appointment->service_description }}
                                </div>
                            </td>
                            <td>
                                @switch($appointment->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-primary">Dikonfirmasi</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-info">Dalam Proses</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">Unknown</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($appointment->status != 'completed' && $appointment->status != 'cancelled')
                                    <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-success" title="Tandai Selesai">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Janji</div>
                            <div class="h5">{{ $appointments->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Menunggu</div>
                            <div class="h5">{{ $appointments->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Dalam Proses</div>
                            <div class="h5">{{ $appointments->where('status', 'in_progress')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Selesai</div>
                            <div class="h5">{{ $appointments->where('status', 'completed')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline View -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Timeline Janji Hari Ini
        </div>
        <div class="card-body">
            @if($appointments->isEmpty())
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada janji untuk ditampilkan</h5>
            </div>
            @else
            <div class="timeline">
                @foreach($appointments->sortBy('appointment_time') as $appointment)
                <div class="timeline-item {{ $appointment->status == 'completed' ? 'completed' : ($appointment->status == 'cancelled' ? 'cancelled' : '') }}">
                    <div class="timeline-time">
                        {{ $appointment->appointment_time->format('H:i') }}
                        <div class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                            {{ strtoupper($appointment->status) }}
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between">
                            <h5>{{ $appointment->customer_name }}</h5>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <p class="mb-1">
                            <i class="fas fa-car me-1"></i>
                            @if($appointment->vehicle)
                                {{ $appointment->vehicle->name }} ({{ $appointment->vehicle->type }})
                            @else
                                <span class="text-muted">Tidak ada data kendaraan</span>
                            @endif
                        </p>
                        <p class="mb-1 text-truncate">
                            <i class="fas fa-tools me-1"></i>
                            {{ $appointment->service_description }}
                        </p>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-phone me-1"></i>
                            {{ $appointment->customer_phone }}
                            @if($appointment->customer_email)
                                • <i class="fas fa-envelope me-1"></i>
                                {{ $appointment->customer_email }}
                            @endif
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 50px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        border-left: 2px solid #dee2e6;
        padding-left: 20px;
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
    }
    .timeline-item.completed {
        opacity: 0.7;
    }
    .timeline-item.cancelled {
        opacity: 0.5;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0d6efd;
        border: 2px solid white;
    }
    .timeline-item.completed:before {
        background: #198754;
    }
    .timeline-item.cancelled:before {
        background: #dc3545;
    }
    .timeline-time {
        position: absolute;
        left: -50px;
        width: 40px;
        text-align: right;
        font-weight: bold;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        position: relative;
    }
    .timeline-content:after {
        content: '';
        position: absolute;
        left: -10px;
        top: 15px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
        border-right: 10px solid #f8f9fa;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#todayAppointmentsTable').DataTable({
            responsive: true,
            order: [[3, 'asc']], // Sort by time
            columnDefs: [
                { orderable: false, targets: [6] } // Disable sorting on action column
            ]
        });

        // Auto hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);

        // Refresh page every 5 minutes to update status
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes
    });
</script>
@endsection