@extends('layouts.layout')

@section('title', 'Detail Janji Temu')

@section('page-title', 'Detail Janji Temu Servis')
@section('page-subtitle', 'Informasi lengkap janji servis kendaraan')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-check me-1"></i>
                            Detail Janji Servis
                        </div>
                        <div class="badge bg-light text-dark">
                            Kode: {{ $appointment->tracking_code }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Informasi Pelanggan</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Nama</th>
                                        <td>{{ $appointment->customer_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telepon</th>
                                        <td>{{ $appointment->customer_phone }}</td>
                                    </tr>
                                    @if($appointment->customer_email)
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $appointment->customer_email }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Detail Janji</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Tanggal</th>
                                        <td>{{ $appointment->appointment_date->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu</th>
                                        <td>{{ $appointment->appointment_time->format('H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @switch($appointment->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
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
                                    </tr>
                                    <tr>
                                        <th>Dibuat Pada</th>
                                        <td>{{ $appointment->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2">Informasi Kendaraan</h5>
                            @if($appointment->vehicle)
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6>{{ $appointment->vehicle->brand }}</h6>
                                            <p class="mb-1"><strong>Model:</strong> {{ $appointment->vehicle->model }}</p>
                                            <p class="mb-1"><strong>Tipe:</strong> {{ $appointment->vehicle->type }}</p>
                                            <p class="mb-1"><strong>Tahun:</strong> {{ $appointment->vehicle->year ?? '-' }}</p>
                                            <p class="mb-0"><strong>No. Polisi:</strong> {{ $appointment->vehicle->license_plate ?? '-' }}</p>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">ID: {{ $appointment->vehicle->id }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Tidak ada informasi kendaraan yang terkait dengan janji ini
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Deskripsi Servis</h5>
                            <div class="card">
                                <div class="card-body">
                                    {!! nl2br(e($appointment->service_description)) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Catatan Tambahan</h5>
                            <div class="card">
                                <div class="card-body">
                                    @if($appointment->notes)
                                        {!! nl2br(e($appointment->notes)) !!}
                                    @else
                                        <span class="text-muted">Tidak ada catatan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <div>
                            {{-- Customer hanya bisa edit jika status masih pending --}}
                            @if(auth()->user()->role === 'customer')
                                @if($appointment->status == 'pending')
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning me-2">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                @else
                                    <span class="text-muted small">Janji tidak dapat diubah setelah dikonfirmasi</span>
                                @endif
                            @else
                                {{-- Admin/Owner tetap bisa edit selama tidak completed/cancelled --}}
                                @if($appointment->status != 'completed' && $appointment->status != 'cancelled')
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning me-2">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    
                                    @if(auth()->user()->can('update appointments'))
                                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#statusModal">
                                        <i class="fas fa-sync-alt me-1"></i> Ubah Status
                                    </button>
                                    @endif
                                @endif
                            @endif
                            
                            @if(auth()->user()->can('print appointments'))
                            <a href="{{ route('appointments.print', $appointment->id) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-print me-1"></i> Cetak
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Riwayat Status
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $appointment->status == 'completed' ? 'completed' : '' }}">
                            <div class="timeline-time">
                                {{ $appointment->created_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h6>Janji Dibuat</h6>
                                <p class="mb-0 small">Janji servis berhasil dibuat</p>
                            </div>
                        </div>
                        
                        @if($appointment->status == 'confirmed' || $appointment->status == 'in_progress' || $appointment->status == 'completed')
                        <div class="timeline-item {{ $appointment->status == 'completed' ? 'completed' : '' }}">
                            <div class="timeline-time">
                                {{ $appointment->updated_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h6>Dikonfirmasi</h6>
                                <p class="mb-0 small">Janji telah dikonfirmasi oleh staf</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($appointment->status == 'in_progress' || $appointment->status == 'completed')
                        <div class="timeline-item {{ $appointment->status == 'completed' ? 'completed' : '' }}">
                            <div class="timeline-time">
                                {{ $appointment->updated_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h6>Servis Dimulai</h6>
                                <p class="mb-0 small">Proses servis sedang berlangsung</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($appointment->status == 'completed')
                        <div class="timeline-item completed">
                            <div class="timeline-time">
                                {{ $appointment->updated_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h6>Servis Selesai</h6>
                                <p class="mb-0 small">Servis telah selesai dilaksanakan</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Action Card - Hanya untuk Admin/Owner -->
            @if(auth()->user()->role !== 'customer')
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i>
                    Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($appointment->status == 'pending')
                        <form action="{{ route('appointments.update-status', $appointment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Konfirmasi janji ini?')">
                                <i class="fas fa-check-circle me-1"></i> Konfirmasi Janji
                            </button>
                        </form>
                        @endif
                        
                        @if($appointment->status == 'confirmed')
                        <form action="{{ route('appointments.update-status', $appointment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-info w-100 mb-2" onclick="return confirm('Mulai proses servis?')">
                                <i class="fas fa-play-circle me-1"></i> Mulai Servis
                            </button>
                        </form>
                        @endif
                        
                        @if($appointment->status == 'in_progress')
                        <form action="{{ route('appointments.update-status', $appointment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Selesaikan servis ini?')">
                                <i class="fas fa-check-double me-1"></i> Selesaikan Servis
                            </button>
                        </form>
                        @endif
                        
                        @if($appointment->status != 'completed' && $appointment->status != 'cancelled')
                        <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times-circle me-1"></i> Batalkan Janji
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <!-- Status Info Card untuk Customer -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Status Janji
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @switch($appointment->status)
                            @case('pending')
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-3x text-warning"></i>
                                </div>
                                <h5 class="text-warning">Menunggu Konfirmasi</h5>
                                <p class="small text-muted">Janji Anda sedang menunggu konfirmasi dari tim kami</p>
                                @break
                            @case('confirmed')
                                <div class="mb-3">
                                    <i class="fas fa-check-circle fa-3x text-primary"></i>
                                </div>
                                <h5 class="text-primary">Dikonfirmasi</h5>
                                <p class="small text-muted">Janji Anda telah dikonfirmasi. Harap datang tepat waktu</p>
                                @break
                            @case('in_progress')
                                <div class="mb-3">
                                    <i class="fas fa-tools fa-3x text-info"></i>
                                </div>
                                <h5 class="text-info">Dalam Proses</h5>
                                <p class="small text-muted">Kendaraan Anda sedang dalam proses servis</p>
                                @break
                            @case('completed')
                                <div class="mb-3">
                                    <i class="fas fa-check-double fa-3x text-success"></i>
                                </div>
                                <h5 class="text-success">Selesai</h5>
                                <p class="small text-muted">Servis kendaraan Anda telah selesai</p>
                                @break
                            @case('cancelled')
                                <div class="mb-3">
                                    <i class="fas fa-times-circle fa-3x text-danger"></i>
                                </div>
                                <h5 class="text-danger">Dibatalkan</h5>
                                <p class="small text-muted">Janji servis telah dibatalkan</p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Notes Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-sticky-note me-1"></i>
                    Catatan
                </div>
                <div class="card-body">
                    @if($appointment->notes)
                        <p>{{ $appointment->notes }}</p>
                    @else
                        <p class="text-muted">Tidak ada catatan tambahan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal - Hanya untuk Admin/Owner -->
@if(auth()->user()->role !== 'customer')
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="statusModalLabel">Ubah Status Janji</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Baru</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ $appointment->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan Perubahan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $appointment->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">Batalkan Janji</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('appointments.update-status', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="status" value="cancelled">
                    <p>Apakah Anda yakin ingin membatalkan janji servis ini?</p>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Alasan Pembatalan</label>
                        <textarea class="form-control" id="cancel_reason" name="notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
    .timeline-time {
        position: absolute;
        left: -50px;
        width: 40px;
        text-align: right;
        font-weight: bold;
        color: #6c757d;
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
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Auto focus on cancel reason when modal shown
        $('#cancelModal').on('shown.bs.modal', function () {
            $('#cancel_reason').focus();
        });
    });
</script>
@endsection