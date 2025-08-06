@extends('layouts.layout')

@section('title', 'Laporan Stok Inventori')

@section('page-title', 'Laporan Stok Inventori')
@section('page-subtitle', 'Analisis stok dan nilai inventori suku cadang')

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
            Filter Laporan
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory_transactions.report') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="vehicle_type" class="form-label">Tipe Kendaraan</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-select">
                            <option value="">Semua Tipe</option>
                            @foreach($vehicleTypes as $key => $type)
                                <option value="{{ $key }}" {{ request('vehicle_type') == $key ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stock_status" class="form-label">Status Stok</label>
                        <select name="stock_status" id="stock_status" class="form-select">
                            @foreach($stockStatus as $key => $status)
                                <option value="{{ $key }}" {{ request('stock_status') == $key ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('inventory_transactions.report') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Item Suku Cadang</div>
                            <div class="h4">{{ number_format($parts->count()) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Kuantitas Stok</div>
                            <div class="h4">{{ number_format($totalStock) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-warehouse fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Nilai Stok</div>
                            <div class="h4">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert Cards for Stock Status -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Stok di Bawah Minimum</h6>
                            <p class="mb-0 text-muted">{{ $parts->filter(function($part) { return $part->stock < $part->min_stock; })->count() }} item perlu restock</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-warning fs-6">{{ $parts->filter(function($part) { return $part->stock < $part->min_stock; })->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Stok Habis</h6>
                            <p class="mb-0 text-muted">{{ $parts->where('stock', 0)->count() }} item tidak tersedia</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-danger fs-6">{{ $parts->where('stock', 0)->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Report Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-chart-bar me-1"></i>
                    Laporan Detail Stok Inventori
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('inventory_transactions.movement') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list me-1"></i> Laporan Pergerakan
                        </a>
                        <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Transaksi
                        </a>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="stockReportTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode Part</th>
                            <th>Nama Suku Cadang</th>
                            <th>Tipe Kendaraan</th>
                            <th>Stok Saat Ini</th>
                            <th>Stok Minimum</th>
                            <th>Harga Satuan</th>
                            <th>Nilai Stok</th>
                            <th>Total Masuk</th>
                            <th>Total Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parts as $index => $part)
                        <tr class="{{ $part->stock == 0 ? 'table-danger' : ($part->stock < $part->min_stock ? 'table-warning' : '') }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($part->part_number)
                                    <span class="fw-bold">{{ $part->part_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $part->name }}</div>
                                @if($part->description)
                                    <small class="text-muted">{{ Str::limit($part->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($part->vehicle_type)
                                    @case('motorcycle')
                                        <span class="badge bg-primary">Motor</span>
                                        @break
                                    @case('electric_bike')
                                        <span class="badge bg-success">Sepeda Listrik</span>
                                        @break
                                    @case('both')
                                        <span class="badge bg-info">Keduanya</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $part->vehicle_type }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $part->stock == 0 ? 'text-danger' : ($part->stock < $part->min_stock ? 'text-warning' : 'text-success') }}">
                                    {{ number_format($part->stock) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ number_format($part->min_stock) }}</span>
                            </td>
                            <td class="text-end">
                                Rp {{ number_format($part->cost, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <span class="fw-bold">Rp {{ number_format($part->stock * $part->cost, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-success">{{ number_format($part->total_in ?? 0) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-danger">{{ number_format($part->total_out ?? 0) }}</span>
                            </td>
                            <td class="text-center">
                                @if($part->stock == 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Habis
                                    </span>
                                @elseif($part->stock < $part->min_stock)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Rendah
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada data laporan</h5>
                                    <p class="text-muted">Tidak ada suku cadang yang sesuai dengan filter yang dipilih.</p>
                                    <a href="{{ route('inventory_transactions.report') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-times me-1"></i> Reset Filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($parts->count() > 0)
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="7" class="text-end">TOTAL:</th>
                            <th class="text-end">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</th>
                            <th class="text-center">{{ number_format($parts->sum('total_in')) }}</th>
                            <th class="text-center">{{ number_format($parts->sum('total_out')) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .btn, .card-header .btn-group, .alert {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#stockReportTable').DataTable({
            paging: true,
            pageLength: 25,
            info: true,
            searching: true,
            ordering: true,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm d-none'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm d-none'
                }
            ],
            columnDefs: [
                { orderable: false, targets: [10] }, // Disable sorting on status column
                { className: 'text-center', targets: [0, 4, 5, 8, 9, 10] },
                { className: 'text-end', targets: [6, 7] }
            ],
            order: [[4, 'asc']], // Order by stock (ascending) to show low stock first
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
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
    
    // Export to Excel function
    function exportToExcel() {
        $('#stockReportTable').DataTable().button('.buttons-excel').trigger();
    }
    
    // Highlight critical stock levels
    $(document).ready(function() {
        // Add tooltips for status badges
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Add warning animation for critical stock
        $('.table-danger, .table-warning').each(function() {
            $(this).find('td:nth-child(5)').addClass('animate__animated animate__pulse animate__infinite animate__slow');
        });
    });
</script>

<!-- Add animate.css for animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- Add DataTables Buttons extension -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
@endsection