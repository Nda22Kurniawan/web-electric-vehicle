<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Work Order #{{ $workOrder->work_order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .receipt-container {
                margin: 0;
                padding: 10px;
            }
            
            .btn {
                display: none;
            }
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .receipt-container {
            background-color: white;
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 15px;
        }

        .receipt-number {
            font-size: 16px;
            color: #333;
            margin-top: 5px;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            min-width: 120px;
        }

        .info-value {
            color: #555;
        }

        .table-custom {
            margin-bottom: 20px;
        }

        .table-custom th {
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
            font-weight: bold;
            padding: 12px 8px;
        }

        .table-custom td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }

        .total-final {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .payment-info {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 60px;
            margin-bottom: 10px;
        }

        .thank-you {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background-color: #f1f8ff;
            border-radius: 5px;
            color: #0066cc;
            font-style: italic;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-paid {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0,0,0,0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt-container position-relative">
        <!-- Watermark -->
        @if($workOrder->payment_status == 'paid')
        <div class="watermark">LUNAS</div>
        @endif

        <!-- Print Button -->
        <div class="no-print text-end mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Cetak Kwitansi
            </button>
            <a href="{{ route('work-orders.show', $workOrder->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Header -->
        <div class="receipt-header">
            <div class="company-logo">BENGKEL MOTOR XYZ</div>
            <div class="company-info">
                Jl. Raya Bengkel No. 123, Kota Jakarta<br>
                Telp: (021) 1234-5678 | Email: info@bengkelxyz.com<br>
                NPWP: 12.345.678.9-012.000
            </div>
            <div class="receipt-title">KWITANSI PEMBAYARAN</div>
            <div class="receipt-number">No: {{ $workOrder->work_order_number }}</div>
        </div>

        <!-- Work Order Info -->
        <div class="info-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">Tanggal:</span>
                        <span class="info-value">{{ $workOrder->created_at->format('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            @if($workOrder->status == 'completed')
                                <span class="status-badge status-completed">SELESAI</span>
                            @else
                                <span class="status-badge">{{ strtoupper($workOrder->status) }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mekanik:</span>
                        <span class="info-value">{{ $workOrder->mechanic->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">Nama Pelanggan:</span>
                        <span class="info-value">{{ $workOrder->customer_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. Telepon:</span>
                        <span class="info-value">{{ $workOrder->customer_phone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kendaraan:</span>
                        <span class="info-value">
                            @if($workOrder->vehicle)
                                {{ $workOrder->vehicle->brand }} {{ $workOrder->vehicle->model }} ({{ $workOrder->vehicle->license_plate }})
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services -->
        @if($workOrder->services->count() > 0)
        <div class="mb-4">
            <h5 class="mb-3"><i class="fas fa-cogs me-2"></i>Layanan</h5>
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 40%">Nama Layanan</th>
                        <th style="width: 10%" class="text-center">Qty</th>
                        <th style="width: 20%" class="text-end">Harga Satuan</th>
                        <th style="width: 25%" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrder->services as $index => $service)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $service->service->name }}</td>
                        <td class="text-center">{{ $service->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($service->quantity * $service->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Parts -->
        @if($workOrder->parts->count() > 0)
        <div class="mb-4">
            <h5 class="mb-3"><i class="fas fa-puzzle-piece me-2"></i>Spare Parts</h5>
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 40%">Nama Part</th>
                        <th style="width: 10%" class="text-center">Qty</th>
                        <th style="width: 20%" class="text-end">Harga Satuan</th>
                        <th style="width: 25%" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrder->parts as $index => $part)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $part->part->name }}</td>
                        <td class="text-center">{{ $part->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($part->price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($part->quantity * $part->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal Layanan:</span>
                <span>Rp {{ number_format($workOrder->services->sum(function($s) { return $s->quantity * $s->price; }), 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Subtotal Parts:</span>
                <span>Rp {{ number_format($workOrder->parts->sum(function($p) { return $p->quantity * $p->price; }), 0, ',', '.') }}</span>
            </div>
            <div class="total-row total-final">
                <span>TOTAL KESELURUHAN:</span>
                <span>Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Information -->
        @if($workOrder->payments->count() > 0)
        <div class="payment-info">
            <h6 class="mb-3"><i class="fas fa-money-bill-wave me-2"></i>Informasi Pembayaran</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="total-row">
                        <span>Total Dibayar:</span>
                        <span class="fw-bold">Rp {{ number_format($workOrder->payments->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Sisa Pembayaran:</span>
                        <span class="fw-bold {{ $workOrder->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($workOrder->remaining_balance, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="total-row">
                        <span>Status Pembayaran:</span>
                        <span>
                            @if($workOrder->payment_status == 'paid')
                                <span class="status-badge status-paid">LUNAS</span>
                            @elseif($workOrder->payment_status == 'partial')
                                <span class="status-badge" style="background-color: #fff3cd; color: #856404;">SEBAGIAN</span>
                            @else
                                <span class="status-badge" style="background-color: #f8d7da; color: #721c24;">BELUM BAYAR</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            @if($workOrder->payments->count() > 0)
            <div class="mt-3">
                <small class="fw-bold">Detail Pembayaran:</small>
                @foreach($workOrder->payments as $payment)
                <div class="d-flex justify-content-between mt-1">
                    <small>{{ $payment->payment_date->format('d/m/Y') }} - 
                        @if($payment->payment_method == 'cash')
                            Tunai
                        @elseif($payment->payment_method == 'transfer')
                            Transfer
                        @elseif($payment->payment_method == 'qris')
                            QRIS
                        @elseif($payment->payment_method == 'credit_card')
                            Kartu Kredit
                        @else
                            {{ ucfirst($payment->payment_method) }}
                        @endif
                    </small>
                    <small class="fw-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</small>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Diagnosis -->
        @if($workOrder->diagnosis)
        <div class="mt-4 p-3" style="background-color: #f8f9fa; border-left: 4px solid #007bff;">
            <h6 class="mb-2"><i class="fas fa-stethoscope me-2"></i>Diagnosis & Catatan:</h6>
            <p class="mb-0">{{ $workOrder->diagnosis }}</p>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Pelanggan</div>
                <small class="text-muted">({{ $workOrder->customer_name }})</small>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Mekanik</div>
                <small class="text-muted">({{ $workOrder->mechanic->name ?? '-' }})</small>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Admin</div>
                <small class="text-muted">(Bengkel Motor XYZ)</small>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="thank-you">
            <strong>Terima kasih atas kepercayaan Anda!</strong><br>
            <small>Garansi pekerjaan 1 bulan atau 1000 km (yang tercapai lebih dulu)<br>
            Simpan kwitansi ini sebagai bukti pembayaran dan garansi</small>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-4">
            <small class="text-muted">
                Kwitansi ini dicetak pada {{ now()->format('d F Y \p\u\k\u\l H:i') }} WIB<br>
                Dokumen ini sah tanpa tanda tangan basah
            </small>
        </div>
    </div>

    <!-- Bootstrap JS untuk print functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
</body>
</html>