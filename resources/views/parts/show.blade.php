@extends('layouts.layout')

@section('title', 'Detail Suku Cadang')

@section('page-title', 'Detail Suku Cadang')
@section('page-subtitle', 'Informasi lengkap suku cadang')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Part Profile Card -->
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    Informasi Suku Cadang
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-cog fa-3x text-muted"></i>
                        </div>
                        <h4 class="mb-1">{{ $part->name }}</h4>
                        <div class="mb-2">
                            @if($part->part_number)
                                <span class="badge bg-secondary fs-6">{{ $part->part_number }}</span>
                            @endif
                        </div>
                        <div class="mb-2">
                            @switch($part->vehicle_type)
                                @case('motorcycle')
                                    <span class="badge bg-primary">Motor</span>
                                    @break
                                @case('electric_bike')
                                    <span class="badge bg-info">Motor Listrik</span>
                                    @break
                                @case('both')
                                    <span class="badge bg-secondary">Keduanya</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    
                    <div class="row text-start">
                        <div class="col-12 mb-3">
                            <p class="mb-2"><strong>Deskripsi:</strong></p>
                            <p class="text-muted">{{ $part->description ?: 'Tidak ada deskripsi' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Harga Jual:</strong></p>
                            <p class="text-success mb-3 fw-bold">Rp {{ number_format($part->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Harga Beli:</strong></p>
                            <p class="text-muted mb-3">Rp {{ number_format($part->cost, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Terdaftar:</strong></p>
                            <p class="text-muted mb-3">{{ $part->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Terakhir Update:</strong></p>
                            <p class="text-muted mb-3">{{ $part->updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('parts.edit', $part) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('parts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 col-lg-7">
            <!-- Stock Information -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card {{ $part->stock <= $part->min_stock ? 'bg-danger' : 'bg-success' }} text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Stok Saat Ini</div>
                                    <div class="h4">{{ $part->stock }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-boxes fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Minimum Stok</div>
                                    <div class="h4">{{ $part->min_stock }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Nilai Stok</div>
                                    <div class="h5">Rp {{ number_format($part->stock * $part->cost, 0, ',', '.') }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calculator fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Margin</div>
                                    <div class="h5">{{ $part->cost > 0 ? number_format((($part->price - $part->cost) / $part->cost) * 100, 1) : 0 }}%</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-percentage fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Status Alert -->
            @if($part->stock <= $part->min_stock)
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Peringatan Stok Rendah!</strong> 
                Stok suku cadang ini sudah mencapai atau di bawah batas minimum. Segera lakukan pembelian untuk menghindari kehabisan stok.
            </div>
            @endif

            <!-- Financial Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-money-bill-wave me-1"></i>
                    Informasi Finansial
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Harga Beli:</strong></td>
                                    <td class="text-end">Rp {{ number_format($part->cost, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga Jual:</strong></td>
                                    <td class="text-end text-success fw-bold">Rp {{ number_format($part->price, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Margin per Unit:</strong></td>
                                    <td class="text-end text-primary fw-bold">Rp {{ number_format($part->price - $part->cost, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Persentase Margin:</strong></td>
                                    <td class="text-end">{{ $part->cost > 0 ? number_format((($part->price - $part->cost) / $part->cost) * 100, 2) : 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Nilai Stok:</strong></td>
                                    <td class="text-end">Rp {{ number_format($part->stock * $part->cost, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Potensi Keuntungan:</strong></td>
                                    <td class="text-end text-success fw-bold">Rp {{ number_format($part->stock * ($part->price - $part->cost), 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Transactions -->
            @if($part->inventoryTransactions->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Riwayat Transaksi Inventori
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis Transaksi</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($part->inventoryTransactions->sortByDesc('created_at')->take(10) as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @switch($transaction->transaction_type)
                                            @case('purchase')
                                                <span class="badge bg-success">Pembelian</span>
                                                @break
                                            @case('sale')
                                                <span class="badge bg-primary">Penjualan</span>
                                                @break
                                            @case('adjustment')
                                                <span class="badge bg-warning">Penyesuaian</span>
                                                @break
                                            @case('return')
                                                <span class="badge bg-info">Return</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($transaction->transaction_type) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if(in_array($transaction->transaction_type, ['purchase', 'return']))
                                            <span class="text-success">+{{ $transaction->quantity }}</span>
                                        @elseif($transaction->transaction_type === 'sale')
                                            <span class="text-danger">-{{ $transaction->quantity }}</span>
                                        @else
                                            <span class="text-warning">{{ $transaction->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->notes ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($part->inventoryTransactions->count() > 10)
                    <div class="text-center mt-3">
                        <small class="text-muted">Menampilkan 10 transaksi terbaru dari {{ $part->inventoryTransactions->count() }} total transaksi</small>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Riwayat Transaksi Inventori
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Transaksi</h5>
                    <p class="text-muted">Transaksi inventori akan muncul di sini setelah ada perubahan stok.</p>
                </div>
            </div>
            @endif

            <!-- Additional Information -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informasi Tambahan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Status Stok:</h6>
                            @if($part->stock <= 0)
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times-circle me-1"></i> Habis
                                </span>
                            @elseif($part->stock <= $part->min_stock)
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Stok Rendah
                                </span>
                            @else
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i> Normal
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Kompatibilitas:</h6>
                            <p class="text-muted mb-0">
                                @switch($part->vehicle_type)
                                    @case('motorcycle')
                                        Khusus untuk sepeda motor konvensional
                                        @break
                                    @case('electric_bike')
                                        Khusus untuk sepeda motor listrik
                                        @break
                                    @case('both')
                                        Compatible untuk kedua jenis kendaraan
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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