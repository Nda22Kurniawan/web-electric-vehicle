@extends('layouts.layout')

@section('title', 'Hasil Pelacakan Janji')

@section('page-title', 'Hasil Pelacakan Janji Servis')
@section('page-subtitle', 'Detail status janji servis Anda')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-check me-1"></i>
                            Detail Janji Servis
                        </div>
                        <div class="badge bg-primary">
                            Kode: {{ $appointment->tracking_code }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Pelanggan</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0">
                                    <strong>Nama:</strong> {{ $appointment->customer_name }}
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>Telepon:</strong> {{ $appointment->customer_phone }}
                                </li>
                                @if($appointment->customer_email)
                                <li class="list-group-item px-0">
                                    <strong>Email:</strong> {{ $appointment->customer_email }}
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Detail Janji</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0">
                                    <strong>Tanggal:</strong> {{ $appointment->appointment_date->format('d F Y') }}
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>Waktu:</strong> {{ $appointment->appointment_time->format('H:i') }}
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>Status:</strong> 
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
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Informasi Kendaraan</h5>
                            <div class="card">
                                <div class="card-body">
                                    @if($appointment->vehicle)
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6>{{ $appointment->vehicle->name }}</h6>
                                            <p class="mb-1">Tipe: {{ $appointment->vehicle->type }}</p>
                                            <p class="mb-0">Tahun: {{ $appointment->vehicle->year ?? '-' }}</p>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">ID: {{ $appointment->vehicle->id }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-car fa-2x mb-2"></i>
                                        <p>Tidak ada informasi kendaraan</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Deskripsi Servis</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $appointment->service_description }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Catatan Tambahan</h5>
                            <div class="card">
                                <div class="card-body">
                                    @if($appointment->notes)
                                        {{ $appointment->notes }}
                                    @else
                                        <span class="text-muted">Tidak ada catatan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($appointment->status != 'completed' && $appointment->status != 'cancelled')
                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Anda dapat membatalkan janji ini minimal 3 jam sebelum waktu janji temu.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times me-1"></i> Batalkan Janji
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('appointments.track') }}" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i> Lacak Janji Lain
                    </a>
                </div>
            </div>
            
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
                                <h5>Janji Dibuat</h5>
                                <p class="mb-0">Janji servis berhasil dibuat dengan kode {{ $appointment->tracking_code }}</p>
                            </div>
                        </div>
                        
                        @if($appointment->status == 'confirmed' || $appointment->status == 'in_progress' || $appointment->status == 'completed')
                        <div class="timeline-item {{ $appointment->status == 'completed' ? 'completed' : '' }}">
                            <div class="timeline-time">
                                {{ $appointment->updated_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h5>Janji Dikonfirmasi</h5>
                                <p class="mb-0">Bengkel telah mengkonfirmasi janji Anda</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($appointment->status == 'in_progress' || $appointment->status == 'completed')
                        <div class="timeline-item {{ $appointment->status == 'completed' ? 'completed' : '' }}">
                            <div class="timeline-time">
                                {{ $appointment->updated_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h5>Servis Dimulai</h5>
                                <p class="mb-0">Proses servis kendaraan Anda sedang berlangsung</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($appointment->status == 'completed')
                        <div class="timeline-item completed">
                            <div class="timeline-time">
                                {{ $appointment->updated_at->format('d M H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h5>Servis Selesai</h5>
                                <p class="mb-0">Proses servis telah selesai dilaksanakan</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
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
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
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
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection