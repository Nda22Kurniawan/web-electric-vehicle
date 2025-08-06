@extends('layouts.layout')

@section('title', 'Transaksi Inventori')

@section('page-title', 'Manajemen Transaksi Inventori')
@section('page-subtitle', 'Riwayat pergerakan stok suku cadang')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Transaksi
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory-transactions.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="part_id" class="form-label">Suku Cadang</label>
                        <select name="part_id" id="part_id" class="form-select">
                            <option value="">Semua Suku Cadang</option>
                            @foreach($parts as $part)
                                <option value="{{ $part->id }}" {{ request('part_id') == $part->id ? 'selected' : '' }}>
                                    {{ $part->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="transaction_type" class="form-label">Jenis Transaksi</label>
                        <select name="transaction_type" id="transaction_type" class="form-select">
                            <option value="">Semua Jenis</option>
                            @foreach($transactionTypes as $key => $type)
                                <option value="{{ $key }}" {{ request('transaction_type') == $key ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-exchange-alt me-1"></i>
                    Riwayat Transaksi Inventori
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('inventory_transactions.report') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar me-1"></i> Laporan Stok
                        </a>
                        <a href="{{ route('inventory_transactions.movement') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list me-1"></i> Laporan Pergerakan
                        </a>
                        <a href="{{ route('inventory-transactions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Tambah Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="transactionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal/Waktu</th>
                            <th>Suku Cadang</th>
                            <th>Jenis Transaksi</th>
                            <th>Kuantitas</th>
                            <th>Work Order</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $transactions->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $transaction->part->name }}</div>
                                @if($transaction->part->part_number)
                                    <small class="text-muted">{{ $transaction->part->part_number }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($transaction->transaction_type)
                                    @case('purchase')
                                        <span class="badge bg-success">
                                            <i class="fas fa-shopping-cart me-1"></i>
                                            {{ $transactionTypes[$transaction->transaction_type] }}
                                        </span>
                                        @break
                                    @case('sales')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-dollar-sign me-1"></i>
                                            {{ $transactionTypes[$transaction->transaction_type] }}
                                        </span>
                                        @break
                                    @case('adjustment')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-edit me-1"></i>
                                            {{ $transactionTypes[$transaction->transaction_type] }}
                                        </span>
                                        @break
                                    @case('return')
                                        <span class="badge bg-info">
                                            <i class="fas fa-undo me-1"></i>
                                            {{ $transactionTypes[$transaction->transaction_type] }}
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $transaction->transaction_type }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($transaction->quantity > 0)
                                    <span class="text-success fw-bold">
                                        <i class="fas fa-plus me-1"></i>{{ $transaction->quantity }}
                                    </span>
                                @else
                                    <span class="text-danger fw-bold">
                                        <i class="fas fa-minus me-1"></i>{{ abs($transaction->quantity) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->workOrder)
                                    <a href="{{ route('work-orders.show', $transaction->workOrder->id) }}" class="text-decoration-none">
                                        <span class="badge bg-info">WO-{{ $transaction->workOrder->id }}</span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->notes)
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $transaction->notes }}">
                                        {{ $transaction->notes }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('inventory-transactions.show', $transaction->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada transaksi inventori</h5>
                                    <p class="text-muted">Belum ada riwayat transaksi yang sesuai dengan filter yang dipilih.</p>
                                    @if(request()->hasAny(['part_id', 'transaction_type', 'date_from', 'date_to']))
                                        <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-times me-1"></i> Reset Filter
                                        </a>
                                    @endif
                                    <a href="{{ route('inventory-transactions.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Tambah Transaksi
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $transactions->firstItem() }} sampai {{ $transactions->lastItem() }} 
                    dari {{ $transactions->total() }} transaksi
                </div>
                <div>
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Transaksi</div>
                            <div class="h5">{{ $transactions->total() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
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
                            <div class="small text-white-50">Transaksi Masuk</div>
                            <div class="h5">{{ $transactions->where('quantity', '>', 0)->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-plus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Transaksi Keluar</div>
                            <div class="h5">{{ $transactions->where('quantity', '<', 0)->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-minus fa-2x"></i>
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
                            <div class="small text-white-50">Hari Ini</div>
                            <div class="h5">{{ $transactions->where('created_at', '>=', today())->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
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
        // Initialize DataTable with minimal configuration
        $('#transactionsTable').DataTable({
            paging: false,
            info: false,
            searching: false, // Disable since we have custom search
            ordering: true,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [7] } // Disable sorting on action column
            ]
        });
        
        // Auto hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
        
        // Set max date for date inputs
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date_from').setAttribute('max', today);
        document.getElementById('date_to').setAttribute('max', today);
        
        // Date range validation
        document.getElementById('date_from').addEventListener('change', function() {
            const dateFrom = this.value;
            const dateTo = document.getElementById('date_to');
            if (dateFrom) {
                dateTo.setAttribute('min', dateFrom);
            }
        });
        
        document.getElementById('date_to').addEventListener('change', function() {
            const dateTo = this.value;
            const dateFrom = document.getElementById('date_from');
            if (dateTo) {
                dateFrom.setAttribute('max', dateTo);
            }
        });
    });
</script>
@endsection