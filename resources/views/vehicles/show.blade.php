@extends('layouts.layout')

@section('title', 'Detail Kendaraan')

@section('page-title', 'Manajemen Kendaraan')
@section('page-subtitle', 'Detail informasi kendaraan')

@section('content')
<div class="container-fluid px-4">
    <!-- Vehicle Information Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-motorcycle me-1"></i>
                Detail Kendaraan
            </div>
            <div>
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Customer Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user me-1"></i> Informasi Pelanggan</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold" style="width: 40%;">Nama:</td>
                                    <td>{{ $vehicle->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Telepon:</td>
                                    <td>
                                        <a href="tel:{{ $vehicle->customer->phone }}" class="text-decoration-none">
                                            {{ $vehicle->customer->phone }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>
                                        @if($vehicle->customer->email)
                                            <a href="mailto:{{ $vehicle->customer->email }}" class="text-decoration-none">
                                                {{ $vehicle->customer->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Role:</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($vehicle->customer->role) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-motorcycle me-1"></i> Informasi Kendaraan</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold" style="width: 40%;">Jenis:</td>
                                    <td>
                                        @if($vehicle->type == 'motorcycle')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-motorcycle me-1"></i> Sepeda Motor
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-bicycle me-1"></i> Sepeda Listrik
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Merek:</td>
                                    <td>{{ $vehicle->brand }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Model:</td>
                                    <td>{{ $vehicle->model }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tahun:</td>
                                    <td>{{ $vehicle->year }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Plat Nomor:</td>
                                    <td>
                                        <span class="badge bg-dark fs-6">{{ $vehicle->license_plate }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Warna:</td>
                                    <td>
                                        @if($vehicle->color)
                                            <div class="d-flex align-items-center">
                                                <div class="color-preview me-2 rounded" 
                                                     style="width: 20px; height: 20px; border: 1px solid #ced4da; background-color: {{ $vehicle->color }}"></div>
                                                {{ $vehicle->color }}
                                            </div>
                                        @else
                                            <span class="text-muted">Tidak diisi</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($vehicle->notes)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-sticky-note me-1"></i> Catatan</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $vehicle->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Appointments Section -->
    @if($vehicle->appointments && $vehicle->appointments->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            Riwayat Janji Temu ({{ $vehicle->appointments->count() }})
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->appointments->sortByDesc('appointment_date') as $appointment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                            <td>{{ $appointment->service_type ?? '-' }}</td>
                            <td>
                                @switch($appointment->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-info">Dikonfirmasi</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
                                @endswitch
                            </td>
                            <td>{{ Str::limit($appointment->notes, 50) ?? '-' }}</td>
                            <td>
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Work Orders Section -->
    @if($vehicle->workOrders && $vehicle->workOrders->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-tools me-1"></i>
            Riwayat Work Order ({{ $vehicle->workOrders->count() }})
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. WO</th>
                            <th>Tanggal</th>
                            <th>Jenis Layanan</th>
                            <th>Status</th>
                            <th>Total Biaya</th>
                            <th>Teknisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->workOrders->sortByDesc('created_at') as $workOrder)
                        <tr>
                            <td>{{ $workOrder->work_order_number ?? '-' }}</td>
                            <td>{{ $workOrder->created_at->format('d/m/Y') }}</td>
                            <td>{{ $workOrder->service_type ?? '-' }}</td>
                            <td>
                                @switch($workOrder->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-info">Sedang Dikerjakan</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($workOrder->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($workOrder->total_cost)
                                    Rp {{ number_format($workOrder->total_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $workOrder->technician->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <div>
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit Kendaraan
            </a>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kendaraan ini?</p>
                <div class="alert alert-warning">
                    <strong>Peringatan:</strong> 
                    <ul class="mb-0">
                        <li><strong>{{ $vehicle->license_plate }}</strong> - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</li>
                        <li>Milik: <strong>{{ $vehicle->customer->name }}</strong></li>
                    </ul>
                </div>
                @if($vehicle->appointments()->exists() || $vehicle->workOrders()->exists())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Tidak dapat dihapus!</strong> Kendaraan ini memiliki riwayat janji temu atau work order.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                @if(!$vehicle->appointments()->exists() && !$vehicle->workOrders()->exists())
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips if needed
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection