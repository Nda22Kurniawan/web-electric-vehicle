@extends('layouts.layout')

@section('title', 'Invoice Work Order')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-4">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <h1 class="h3">INVOICE</h1>
                            <p class="mb-0"><strong>No. WO:</strong> {{ $workOrder->work_order_number }}</p>
                            <p class="mb-0"><strong>Tanggal:</strong> {{ $workOrder->created_at->format('d/m/Y') }}</p>
                            <p class="mb-0"><strong>Status:</strong> 
                                @if($workOrder->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($workOrder->status == 'in_progress')
                                    <span class="badge bg-primary">Dalam Proses</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($workOrder->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <h2 class="h4">{{ config('app.name') }}</h2>
                            <p class="mb-0">{{ config('app.address') }}</p>
                            <p class="mb-0">Telp: {{ config('app.phone') }}</p>
                            <p class="mb-0">Email: {{ config('app.email') }}</p>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h5 class="card-title mb-3">Informasi Pelanggan</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nama:</strong> {{ $workOrder->customer_name }}</p>
                                            <p class="mb-1"><strong>No. Telepon:</strong> {{ $workOrder->customer_phone }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($workOrder->vehicle)
                                            <p class="mb-1"><strong>Kendaraan:</strong> {{ $workOrder->vehicle->brand }} {{ $workOrder->vehicle->model }}</p>
                                            <p class="mb-1"><strong>Plat Nomor:</strong> {{ $workOrder->vehicle->license_plate }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    @if($workOrder->services->count() > 0)
                    <div class="mb-4">
                        <h5 class="mb-3">Layanan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="45%">Nama Layanan</th>
                                        <th width="15%">Qty</th>
                                        <th width="20%">Harga</th>
                                        <th width="20%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workOrder->services as $index => $service)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $service->service->name }}</td>
                                        <td>{{ $service->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($service->quantity * $service->price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total Layanan</th>
                                        <th class="text-end">Rp {{ number_format($workOrder->services->sum(function($s) { return $s->quantity * $s->price; }), 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Parts -->
                    @if($workOrder->parts->count() > 0)
                    <div class="mb-4">
                        <h5 class="mb-3">Spare Parts</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="45%">Nama Part</th>
                                        <th width="15%">Qty</th>
                                        <th width="20%">Harga</th>
                                        <th width="20%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workOrder->parts as $index => $part)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $part->part->name }}</td>
                                        <td>{{ $part->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($part->price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($part->quantity * $part->price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total Parts</th>
                                        <th class="text-end">Rp {{ number_format($workOrder->parts->sum(function($p) { return $p->quantity * $p->price; }), 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Summary -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h5 class="card-title mb-3">Diagnosis</h5>
                                    <p>{{ $workOrder->diagnosis ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h5 class="card-title mb-3">Ringkasan Pembayaran</h5>
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td>Total Layanan:</td>
                                            <td class="text-end">Rp {{ number_format($workOrder->services->sum(function($s) { return $s->quantity * $s->price; }), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Parts:</td>
                                            <td class="text-end">Rp {{ number_format($workOrder->parts->sum(function($p) { return $p->quantity * $p->price; }), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total:</strong></td>
                                            <td class="text-end"><strong>Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Total Dibayar:</td>
                                            <td class="text-end">Rp {{ number_format($workOrder->payments->sum('amount'), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Sisa Pembayaran:</strong></td>
                                            <td class="text-end"><strong>Rp {{ number_format($workOrder->remaining_balance, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if($workOrder->payments->count() > 0)
                    <div class="mt-4">
                        <h5 class="mb-3">Riwayat Pembayaran</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Tanggal</th>
                                        <th width="20%">Jumlah</th>
                                        <th width="20%">Metode</th>
                                        <th width="35%">Referensi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workOrder->payments as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($payment->payment_method == 'cash')
                                                Tunai
                                            @elseif($payment->payment_method == 'transfer')
                                                Transfer
                                            @elseif($payment->payment_method == 'qris')
                                                QRIS
                                            @elseif($payment->payment_method == 'credit_card')
                                                Kartu Kredit
                                            @else
                                                Lainnya
                                            @endif
                                        </td>
                                        <td>{{ $payment->reference_number ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="border-top pt-3">
                                <p class="mb-1"><strong>Mekanik:</strong></p>
                                <p>{{ $workOrder->mechanic->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="border-top pt-3">
                                <p class="mb-1">Hormat Kami,</p>
                                <p class="mt-5"><strong>{{ config('app.name') }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Print Button -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="fas fa-print me-2"></i> Cetak Invoice
                            </button>
                            <a href="{{ route('work-orders.show', $workOrder->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection