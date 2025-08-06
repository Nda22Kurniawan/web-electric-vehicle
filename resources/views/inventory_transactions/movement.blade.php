@extends('layouts.layout')

@section('title', 'Laporan Pergerakan Inventori')

@section('page-title', 'Laporan Pergerakan Inventori')
@section('page-subtitle', 'Analisis pergerakan stok suku cadang dengan running balance')

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
            Filter Laporan Pergerakan
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory_transactions.movement') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="part_id" class="form-label">Suku Cadang</label>
                        <select name="part_id" id="part_id" class="form-select">
                            <option value="">Semua Suku Cadang</option>
                            @foreach($parts as $part)
                                <option value="{{ $part->id }}" {{ request('part_id') == $part->id ? 'selected' : '' }}>
                                    {{ $part->name }} 
                                    @if($part->part_number)
                                        ({{ $part->part_number }})
                                    @endif
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
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('inventory_transactions.movement') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Cards -->
    @if($transactions->count() > 0)
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Transaksi</div>
                            <div class="h4">{{ number_format($transactions->count()) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Masuk</div>
                            <div class="h4">{{ number_format($transactions->where('quantity', '>', 0)->sum('quantity')) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-plus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Keluar</div>
                            <div class="h4">{{ number_format(abs($transactions->where('quantity', '<', 0)->sum('quantity'))) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-minus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Saldo Akhir</div>
                            <div class="h4">{{ number_format($transactions->last()->running_balance ?? 0) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Selected Part Info -->
    @if(request('part_id') && $transactions->count() > 0)
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <i class="fas fa-info-circle me-1"></i>
            Informasi Suku Cadang Terpilih
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-1">{{ $transactions->first()->part->name }}</h5>
                    @if($transactions->first()->part->part_number)
                        <p class="text-muted mb-1">Kode: {{ $transactions->first()->part->part_number }}</p>
                    @endif
                    @if($transactions->first()->part->description)
                        <p class="text-muted mb-0">{{ $transactions->first()->part->description }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <div class="row">
                        <div class="col-6">
                            <div class="small text-muted">Stok Saat Ini</div>
                            <div class="h5 text-primary">{{ number_format($transactions->first()->part->stock) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Stok Minimum</div>
                            <div class="h5 text-warning">{{ number_format($transactions->first()->part->min_stock) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Main Report Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-1"></i>
                    Laporan Detail Pergerakan Inventori
                    @if(request('part_id'))
                        - {{ $transactions->first()->part->name ?? 'Suku Cadang' }}
                    @endif
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('inventory_transactions.report') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar me-1"></i> Laporan Stok
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
                <table class="table table-bordered table-hover table-sm" id="movementReportTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal/Waktu</th>
                            @if(!request('part_id'))
                            <th width="18%">Suku Cadang</th>
                            @endif
                            <th width="12%">Jenis Transaksi</th>
                            <th width="8%">Masuk</th>
                            <th width="8%">Keluar</th>
                            <th width="8%">Saldo</th>
                            <th width="10%">Work Order</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                            </td>
                            @if(!request('part_id'))
                            <td>
                                <div class="fw-bold">{{ $transaction->part->name }}</div>
                                @if($transaction->part->part_number)
                                    <small class="text-muted d-block">{{ $transaction->part->part_number }}</small>
                                @endif
                            </td>
                            @endif
                            <td>
                                @switch($transaction->transaction_type)
                                    @case('purchase')
                                        <span class="badge bg-success">
                                            <i class="fas fa-shopping-cart me-1"></i>Pembelian
                                        </span>
                                        @break
                                    @case('sales')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-dollar-sign me-1"></i>Penjualan
                                        </span>
                                        @break
                                    @case('adjustment')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-edit me-1"></i>Penyesuaian
                                        </span>
                                        @break
                                    @case('return')
                                        <span class="badge bg-info">
                                            <i class="fas fa-undo me-1"></i>Pengembalian
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $transaction->transaction_type }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                @if($transaction->quantity > 0)
                                    <span class="text-success fw-bold">{{ number_format($transaction->quantity) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($transaction->quantity < 0)
                                    <span class="text-danger fw-bold">{{ number_format(abs($transaction->quantity)) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-bold badge {{ $transaction->running_balance > 0 ? 'bg-success' : ($transaction->running_balance == 0 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($transaction->running_balance) }}
                                </span>
                            </td>
                            <td class="text-center">
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
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $transaction->notes }}">
                                        {{ $transaction->notes }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ !request('part_id') ? '9' : '8' }}" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada data pergerakan</h5>
                                    <p class="text-muted">Tidak ada transaksi yang sesuai dengan filter yang dipilih.</p>
                                    @if(request()->hasAny(['part_id', 'date_from', 'date_to']))
                                        <a href="{{ route('inventory_transactions.movement') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-times me-1"></i> Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count() > 0)
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="{{ !request('part_id') ? '4' : '3' }}" class="text-end">TOTAL:</th>
                            <th class="text-center">{{ number_format($transactions->where('quantity', '>', 0)->sum('quantity')) }}</th>
                            <th class="text-center">{{ number_format(abs($transactions->where('quantity', '<', 0)->sum('quantity'))) }}</th>
                            <th class="text-center">
                                <span class="badge {{ $transactions->last()->running_balance > 0 ? 'bg-success' : ($transactions->last()->running_balance == 0 ? 'bg-warning' : 'bg-danger') }} fs-6">
                                    {{ number_format($transactions->last()->running_balance) }}
                                </span>
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            @if($transactions->count() > 0)
            <!-- Movement Chart -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-1"></i>
                            Grafik Pergerakan Stok
                            @if(request('part_id'))
                                - {{ $transactions->first()->part->name }}
                            @endif
                        </div>
                        <div class="card-body">
                            <canvas id="movementChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
        font-size: 11px;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    body {
        background: white !important;
    }
    
    #movementChart {
        display: none !important;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#movementReportTable').DataTable({
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
                    className: 'btn btn-danger btn-sm d-none',
                    orientation: 'landscape'
                }
            ],
            columnDefs: [
                { className: 'text-center', targets: [0{{ !request('part_id') ? ', 4, 5, 6, 7' : ', 3, 4, 5, 6' }}] },
                { orderable: false, targets: [{{ !request('part_id') ? '8' : '7' }}] }
            ],
            order: [[1, 'asc']], // Order by date
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
        
        // Auto hide success alert
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
        
        // Date validation
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date_from').setAttribute('max', today);
        document.getElementById('date_to').setAttribute('max', today);
        
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
        
        @if($transactions->count() > 0)
        // Initialize Movement Chart
        const ctx = document.getElementById('movementChart').getContext('2d');
        const chartData = {
            labels: [
                @foreach($transactions as $transaction)
                '{{ $transaction->created_at->format("d/m H:i") }}',
                @endforeach
            ],
            datasets: [{
                label: 'Saldo Stok',
                data: [
                    @foreach($transactions as $transaction)
                    {{ $transaction->running_balance }},
                    @endforeach
                ],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Transaksi Masuk',
                data: [
                    @foreach($transactions as $transaction)
                    {{ $transaction->quantity > 0 ? $transaction->quantity : null }},
                    @endforeach
                ],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                type: 'bar',
                yAxisID: 'y1'
            }, {
                label: 'Transaksi Keluar',
                data: [
                    @foreach($transactions as $transaction)
                    {{ $transaction->quantity < 0 ? abs($transaction->quantity) : null }},
                    @endforeach
                ],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                type: 'bar',
                yAxisID: 'y1'
            }]
        };
        
        const config = {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Pergerakan Stok {{ request("part_id") ? $transactions->first()->part->name : "Semua Suku Cadang" }}'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Saldo Stok'
                        },
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Jumlah Transaksi'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        };
        
        new Chart(ctx, config);
        @endif
    });
    
    // Export to Excel function
    function exportToExcel() {
        $('#movementReportTable').DataTable().button('.buttons-excel').trigger();
    }
</script>

<!-- Add DataTables Buttons extension -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
@endsection