@extends('layouts.layout')

@section('title', 'Edit Suku Cadang')

@section('page-title', 'Edit Suku Cadang')
@section('page-subtitle', 'Perbarui informasi suku cadang')

@section('content')
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-1"></i>
                Form Edit Suku Cadang
            </div>
            <div class="card-body">
                <form action="{{ route('parts.update', $part->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Suku Cadang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $part->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="part_number" class="form-label">Nomor Part</label>
                            <input type="text" class="form-control @error('part_number') is-invalid @enderror"
                                id="part_number" name="part_number" value="{{ old('part_number', $part->part_number) }}">
                            @error('part_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="2">{{ old('description', $part->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                                name="price" value="{{ old('price', $part->price) }}" min="0" step="100" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cost" class="form-label">Harga Beli (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('cost') is-invalid @enderror" id="cost"
                                name="cost" value="{{ old('cost', $part->cost) }}" min="0" step="100" required>
                            @error('cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="vehicle_type" class="form-label">Jenis Kendaraan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_type') is-invalid @enderror" id="vehicle_type"
                                name="vehicle_type" required>
                                <option value="">Pilih Jenis Kendaraan</option>
                                <option value="motorcycle" @if(old('vehicle_type', $part->vehicle_type) == 'motorcycle')
                                selected @endif>Motor</option>
                                <option value="electric_bike" @if(old('vehicle_type', $part->vehicle_type) == 'electric_bike')
                                selected @endif>Motor Listrik</option>
                                <option value="both" @if(old('vehicle_type', $part->vehicle_type) == 'both') selected @endif>
                                    Keduanya</option>
                            </select>
                            @error('vehicle_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock"
                                name="stock" value="{{ old('stock', $part->stock) }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="min_stock" class="form-label">Stok Minimum <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                                id="min_stock" name="min_stock" value="{{ old('min_stock', $part->min_stock) }}" min="1"
                                required>
                            @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Status Stok</h6>
                                    <p class="card-text">
                                        @if($part->isLowStock())
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Stok Rendah
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Stok Normal
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('parts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($part->inventoryTransactions->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Riwayat Transaksi Stok
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe Transaksi</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($part->inventoryTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($transaction->transaction_type == 'purchase')
                                                <span class="badge bg-success">Pembelian</span>
                                            @else
                                                <span class="badge bg-info">Penyesuaian</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->quantity }}</td>
                                        <td>{{ $transaction->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // Format input harga saat diketik
        document.getElementById('price').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('cost').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
@endsection