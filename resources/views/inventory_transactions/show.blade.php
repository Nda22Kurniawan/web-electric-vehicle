@extends('layouts.layout')

@section('title', 'Detail Transaksi Inventori')

@section('page-title', 'Detail Transaksi Inventori')
@section('page-subtitle', 'Informasi lengkap transaksi inventori')

@section('content')
<div class="container-fluid px-4">
    
    <!-- Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}" class="text-decoration-none">Transaksi Inventori</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail #{{ $inventoryTransaction->id }}</li>
            </ol>
        </nav>
        <div>
            <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Transaction Detail Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-receipt me-2"></i>
                    <strong>Transaksi #{{ $inventoryTransaction->id }}</strong>
                </div>
                <div>
                    @switch($inventoryTransaction->transaction_type)
                        @case('purchase')
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-shopping-cart me-1"></i> Pembelian
                            </span>
                            @break
                        @case('sales')
                            <span class="badge bg-info fs-6">
                                <i class="fas fa-dollar-sign me-1"></i> Penjualan
                            </span>
                            @break
                        @case('adjustment')
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-edit me-1"></i> Penyesuaian
                            </span>
                            @break
                        @case('return')
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-undo me-1"></i> Pengembalian
                            </span>
                            @break
                        @default
                            <span class="badge bg-light text-dark fs-6">{{ ucfirst($inventoryTransaction->transaction_type) }}</span>
                    @endswitch
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-1"></i> Informasi Transaksi
                    </h6>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted" style="width: 40%;">ID Transaksi:</td>
                                <td>{{ $inventoryTransaction->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Tanggal & Waktu:</td>
                                <td>
                                    <div class="fw-bold">{{ $inventoryTransaction->created_at->format('d F Y') }}</div>
                                    <small class="text-muted">{{ $inventoryTransaction->created_at->format('H:i:s') }} WIB</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Jenis Transaksi:</td>
                                <td>
                                    @switch($inventoryTransaction->transaction_type)
                                        @case('purchase')
                                            <span class="badge bg-success">
                                                <i class="fas fa-shopping-cart me-1"></i> Pembelian
                                            </span>
                                            @break
                                        @case('sales')
                                            <span class="badge bg-info">
                                                <i class="fas fa-dollar-sign me-1"></i> Penjualan
                                            </span>
                                            @break
                                        @case('adjustment')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-edit me-1"></i> Penyesuaian
                                            </span>
                                            @break
                                        @case('return')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-undo me-1"></i> Pengembalian
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ ucfirst($inventoryTransaction->transaction_type) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Kuantitas:</td>
                                <td>
                                    @if($inventoryTransaction->quantity > 0)
                                        <span class="text-success fw-bold fs-5">
                                            <i class="fas fa-plus me-1"></i>{{ $inventoryTransaction->quantity }} unit
                                        </span>
                                        <small class="text-muted d-block">Masuk ke inventori</small>
                                    @else
                                        <span class="text-danger fw-bold fs-5">
                                            <i class="fas fa-minus me-1"></i>{{ abs($inventoryTransaction->quantity) }} unit
                                        </span>
                                        <small class="text-muted d-block">Keluar dari inventori</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Work Order:</td>
                                <td>
                                    @if($inventoryTransaction->workOrder)
                                        <a href="{{ route('work-orders.show', $inventoryTransaction->workOrder->id) }}" class="btn btn-sm btn-outline-info text-decoration-none">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            WO-{{ $inventoryTransaction->workOrder->id }}
                                        </a>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                {{ $inventoryTransaction->workOrder->customer_name ?? 'Work Order terkait' }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus me-1"></i> Tidak terkait work order
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-lg-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-cog me-1"></i> Informasi Suku Cadang
                    </h6>
                    
                    <div class="card border">
                        <div class="card-body">
                            <h6 class="card-title mb-3">{{ $inventoryTransaction->part->name }}</h6>
                            
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    @if($inventoryTransaction->part->part_number)
                                    <tr>
                                        <td class="fw-bold text-muted" style="width: 40%;">Part Number:</td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $inventoryTransaction->part->part_number }}</code>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="fw-bold text-muted">Kategori:</td>
                                        <td>{{ $inventoryTransaction->part->category ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Jenis Kendaraan:</td>
                                        <td>
                                            @switch($inventoryTransaction->part->vehicle_type)
                                                @case('motorcycle')
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-motorcycle me-1"></i> Motor
                                                    </span>
                                                    @break
                                                @case('electric_bike')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-bicycle me-1"></i> Sepeda Listrik
                                                    </span>
                                                    @break
                                                @case('both')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-universal-access me-1"></i> Keduanya
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="text-muted">-</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Stok Saat Ini:</td>
                                        <td>
                                            <span class="fw-bold {{ $inventoryTransaction->part->stock <= $inventoryTransaction->part->min_stock ? 'text-danger' : 'text-success' }}">
                                                {{ $inventoryTransaction->part->stock }} unit
                                            </span>
                                            @if($inventoryTransaction->part->min_stock)
                                                <small class="text-muted d-block">Min: {{ $inventoryTransaction->part->min_stock }} unit</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($inventoryTransaction->part->cost)
                                    <tr>
                                        <td class="fw-bold text-muted">Harga Satuan:</td>
                                        <td class="fw-bold">Rp {{ number_format($inventoryTransaction->part->cost, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes Section -->
            @if($inventoryTransaction->notes)
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-sticky-note me-1"></i> Catatan
                    </h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0">{{ $inventoryTransaction->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Timeline -->
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-history me-1"></i> Timeline
                    </h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Transaksi Dibuat</h6>
                                <p class="timeline-text">
                                    {{ $inventoryTransaction->created_at->format('d F Y, H:i:s') }} WIB
                                </p>
                                <small class="text-muted">{{ $inventoryTransaction->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        
                        @if($inventoryTransaction->updated_at != $inventoryTransaction->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Terakhir Diperbarui</h6>
                                <p class="timeline-text">
                                    {{ $inventoryTransaction->updated_at->format('d F Y, H:i:s') }} WIB
                                </p>
                                <small class="text-muted">{{ $inventoryTransaction->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Transaksi ini tidak dapat diubah atau dihapus untuk menjaga integritas data inventori.
                </div>
                <div>
                    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i> Daftar Transaksi
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #6c757d;
}

@media print {
    .btn, .card-header, nav {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto focus on back button for keyboard navigation
        $(document).keydown(function(e) {
            if (e.key === 'Escape') {
                window.location.href = "{{ route('inventory-transactions.index') }}";
            }
        });
        
        // Add tooltip to part number
        $('[title]').tooltip();
    });
</script>
@endsection