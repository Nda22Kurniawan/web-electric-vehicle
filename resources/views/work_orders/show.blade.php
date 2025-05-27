@extends('layouts.layout')

@section('title', 'Detail Work Order')

@section('page-title', 'Detail Work Order')
@section('page-subtitle', 'Informasi lengkap work order')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Work Order Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Work Order #{{ $workOrder->work_order_number }}
                            </h5>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('work-orders.edit', $workOrder->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('work-orders.invoice', $workOrder->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-invoice me-1"></i> Invoice
                            </a>
                            <a href="{{ route('work-orders.receipt', $workOrder->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-receipt me-1"></i> Receipt
                            </a>
                            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($workOrder->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($workOrder->status == 'in_progress')
                                            <span class="badge bg-primary">In Progress</span>
                                        @elseif($workOrder->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($workOrder->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Pembayaran:</strong></td>
                                    <td>
                                        @if($workOrder->payment_status == 'unpaid')
                                            <span class="badge bg-danger">Belum Bayar</span>
                                        @elseif($workOrder->payment_status == 'partial')
                                            <span class="badge bg-warning">Sebagian</span>
                                        @elseif($workOrder->payment_status == 'paid')
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat:</strong></td>
                                    <td>{{ $workOrder->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($workOrder->start_time)
                                <tr>
                                    <td><strong>Waktu Mulai:</strong></td>
                                    <td>{{ $workOrder->start_time->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($workOrder->end_time)
                                <tr>
                                    <td><strong>Waktu Selesai:</strong></td>
                                    <td>{{ $workOrder->end_time->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <div class="text-end">
                                    @if($workOrder->status != 'completed' && $workOrder->status != 'cancelled')
                                    <form action="{{ route('work-orders.update-status', $workOrder->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-2">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" {{ $workOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $workOrder->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $workOrder->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $workOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </form>
                                    @endif
                                    
                                    @if($workOrder->payment_status != 'paid' && $workOrder->status != 'cancelled')
                                    <a href="{{ route('work-orders.create-payment', $workOrder->id) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-money-bill me-1"></i> Tambah Pembayaran
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer & Vehicle Info -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-user me-1"></i> Informasi Pelanggan & Kendaraan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h6 class="text-primary">Pelanggan</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td>{{ $workOrder->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. Telepon:</strong></td>
                                    <td>{{ $workOrder->customer_phone }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        @if($workOrder->vehicle)
                        <div class="col-12">
                            <h6 class="text-primary">Kendaraan</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Jenis:</strong></td>
                                    <td>
                                        @if($workOrder->vehicle->type == 'motorcycle')
                                            <span class="badge bg-primary">Sepeda Motor</span>
                                        @elseif($workOrder->vehicle->type == 'electric_bike')
                                            <span class="badge bg-success">Sepeda Listrik</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Merek & Model:</strong></td>
                                    <td>{{ $workOrder->vehicle->brand }} {{ $workOrder->vehicle->model }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Plat Nomor:</strong></td>
                                    <td>{{ $workOrder->vehicle->license_plate }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tahun:</strong></td>
                                    <td>{{ $workOrder->vehicle->year }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Warna:</strong></td>
                                    <td>{{ $workOrder->vehicle->color ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mechanic & Diagnosis -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools me-1"></i> Informasi Pekerjaan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h6 class="text-primary">Mekanik</h6>
                            <p class="mb-0">{{ $workOrder->mechanic->name ?? '-' }}</p>
                        </div>
                        
                        @if($workOrder->appointment)
                        <div class="col-12 mb-3">
                            <h6 class="text-primary">Appointment</h6>
                            <p class="mb-0">
                                <a href="{{ route('appointments.show', $workOrder->appointment->id) }}" class="text-decoration-none">
                                    Appointment #{{ $workOrder->appointment->id }} - {{ $workOrder->appointment->appointment_date->format('d/m/Y H:i') }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        <div class="col-12">
                            <h6 class="text-primary">Diagnosis</h6>
                            <p class="mb-0">{{ $workOrder->diagnosis ?? 'Belum ada diagnosis' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services -->
    @if($workOrder->services->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cogs me-1"></i> Layanan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Layanan</th>
                                    <th>Quantity</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrder->services as $index => $workOrderService)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $workOrderService->service->name }}</td>
                                    <td>{{ $workOrderService->quantity }}</td>
                                    <td>Rp {{ number_format($workOrderService->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($workOrderService->quantity * $workOrderService->price, 0, ',', '.') }}</td>
                                    <td>{{ $workOrderService->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="4" class="text-end">Total Layanan:</th>
                                    <th>Rp {{ number_format($workOrder->services->sum(function($s) { return $s->quantity * $s->price; }), 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Parts -->
    @if($workOrder->parts->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-puzzle-piece me-1"></i> Spare Parts</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Part</th>
                                    <th>Kode Part</th>
                                    <th>Quantity</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrder->parts as $index => $workOrderPart)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $workOrderPart->part->name }}</td>
                                    <td>{{ $workOrderPart->part->part_code ?? '-' }}</td>
                                    <td>{{ $workOrderPart->quantity }}</td>
                                    <td>Rp {{ number_format($workOrderPart->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($workOrderPart->quantity * $workOrderPart->price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="5" class="text-end">Total Parts:</th>
                                    <th>Rp {{ number_format($workOrder->parts->sum(function($p) { return $p->quantity * $p->price; }), 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Payments -->
    <div class="row mb-4">
        <div class="col-md-8">
            @if($workOrder->payments->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-money-bill me-1"></i> Riwayat Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Referensi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrder->payments as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($payment->payment_method == 'cash')
                                            <span class="badge bg-success">Tunai</span>
                                        @elseif($payment->payment_method == 'transfer')
                                            <span class="badge bg-primary">Transfer</span>
                                        @elseif($payment->payment_method == 'qris')
                                            <span class="badge bg-info">QRIS</span>
                                        @elseif($payment->payment_method == 'credit_card')
                                            <span class="badge bg-warning">Kartu Kredit</span>
                                        @else
                                            <span class="badge bg-secondary">Lainnya</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->reference_number ?? '-' }}</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-money-bill me-1"></i> Riwayat Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Belum ada pembayaran</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calculator me-1"></i> Ringkasan Biaya</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Total Layanan:</strong></td>
                            <td class="text-end">Rp {{ number_format($workOrder->services->sum(function($s) { return $s->quantity * $s->price; }), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Parts:</strong></td>
                            <td class="text-end">Rp {{ number_format($workOrder->parts->sum(function($p) { return $p->quantity * $p->price; }), 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Total Keseluruhan:</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Dibayar:</strong></td>
                            <td class="text-end">Rp {{ number_format($workOrder->payments->sum('amount'), 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Sisa Pembayaran:</strong></td>
                            <td class="text-end">
                                <strong class="{{ $workOrder->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($workOrder->remaining_balance, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Auto-submit form when status changes
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                if (confirm('Apakah Anda yakin ingin mengubah status work order?')) {
                    this.form.submit();
                } else {
                    // Reset to original value if cancelled
                    this.selectedIndex = 0;
                }
            });
        }
    });
</script>
@endsection