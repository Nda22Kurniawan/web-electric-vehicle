@extends('layouts.layout')

@section('title', 'Pembayaran Work Order')

@section('page-title', 'Pembayaran Work Order')
@section('page-subtitle', 'Input data pembayaran untuk work order')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Detail Work Order</h5>
        <p><strong>Nomor WO:</strong> {{ $workOrder->work_order_number }}</p>
        <p><strong>Nama Pelanggan:</strong> {{ $workOrder->customer_name }}</p>
        <p><strong>No. Telepon:</strong> {{ $workOrder->customer_phone }}</p>
        <p><strong>Total Tagihan:</strong> Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</p>
        <p><strong>Sudah Dibayar:</strong> Rp {{ number_format($workOrder->payments->sum('amount'), 0, ',', '.') }}</p>
        <p><strong>Sisa Tagihan:</strong> Rp {{ number_format($workOrder->remaining_balance, 0, ',', '.') }}</p>

        <hr>

        <form action="{{ route('work-orders.store-payment', $workOrder->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="amount" class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                <input
                    type="number"
                    name="amount"
                    id="amount"
                    class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount') }}"
                    required>
                @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="">Pilih metode</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" id="payment_date"
                    class="form-control @error('payment_date') is-invalid @enderror"
                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="reference_number" class="form-label">No. Referensi (Opsional)</label>
                <input type="text" name="reference_number" id="reference_number"
                    class="form-control @error('reference_number') is-invalid @enderror"
                    value="{{ old('reference_number') }}">
                @error('reference_number')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Catatan (Opsional)</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('work-orders.show', $workOrder->id) }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>
@endsection