<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    /**
     * Display a listing of the inventory transactions.
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['part', 'workOrder']);
        
        // Filter by part
        if ($request->has('part_id') && $request->part_id) {
            $query->where('part_id', $request->part_id);
        }
        
        // Filter by transaction type
        if ($request->has('transaction_type') && $request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        $parts = Part::orderBy('name')->get();
        $transactionTypes = [
            'purchase' => 'Pembelian',
            'sales' => 'Penjualan',
            'adjustment' => 'Penyesuaian',
            'return' => 'Pengembalian'
        ];
        
        return view('inventory_transactions.index', compact('transactions', 'parts', 'transactionTypes'));
    }

    /**
     * Show the form for creating a new inventory transaction.
     */
    public function create()
    {
        $parts = Part::orderBy('name')->get();
        $transactionTypes = [
            'purchase' => 'Pembelian',
            'adjustment' => 'Penyesuaian'
        ];
        
        return view('inventory_transactions.create', compact('parts', 'transactionTypes'));
    }

    /**
     * Store a newly created inventory transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'quantity' => 'required|integer|not_in:0',
            'transaction_type' => 'required|in:purchase,adjustment',
            'notes' => 'nullable|string',
        ]);

        // Create transaction
        $transaction = InventoryTransaction::create($validated);
        
        // Update part stock
        $part = Part::findOrFail($validated['part_id']);
        $part->stock += $validated['quantity'];
        $part->save();

        return redirect()->route('inventory-transactions.index')
            ->with('success', 'Transaksi inventori berhasil ditambahkan.');
    }

    /**
     * Display the specified inventory transaction.
     */
    public function show(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->load(['part', 'workOrder']);
        return view('inventory_transactions.show', compact('inventoryTransaction'));
    }

    /**
     * Generate inventory report.
     */
    public function report(Request $request)
    {
        // Date range validation
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        $query = Part::withCount([
            'inventoryTransactions as total_in' => function ($query) use ($request) {
                $query->where('quantity', '>', 0);
                
                if ($request->has('date_from') && $request->date_from) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                
                if ($request->has('date_to') && $request->date_to) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
                
                $query->selectRaw('SUM(quantity)');
            },
            'inventoryTransactions as total_out' => function ($query) use ($request) {
                $query->where('quantity', '<', 0);
                
                if ($request->has('date_from') && $request->date_from) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                
                if ($request->has('date_to') && $request->date_to) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
                
                $query->selectRaw('SUM(ABS(quantity))');
            }
        ])
        ->withSum([
            'inventoryTransactions as total_value_in' => function ($query) use ($request) {
                $query->where('quantity', '>', 0)
                    ->where('transaction_type', 'purchase');
                
                if ($request->has('date_from') && $request->date_from) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                
                if ($request->has('date_to') && $request->date_to) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
            }
        ], 'quantity');
        
        // Filter by vehicle type
        if ($request->has('vehicle_type') && $request->vehicle_type) {
            if ($request->vehicle_type != 'both') {
                $query->where(function ($q) use ($request) {
                    $q->where('vehicle_type', $request->vehicle_type)
                      ->orWhere('vehicle_type', 'both');
                });
            }
        }
        
        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
            if ($request->stock_status == 'below_min') {
                $query->whereRaw('stock < min_stock');
            } elseif ($request->stock_status == 'out_of_stock') {
                $query->where('stock', 0);
            } elseif ($request->stock_status == 'available') {
                $query->where('stock', '>', 0);
            }
        }
        
        $parts = $query->orderBy('name')->get();
        
        // Calculate total values
        $totalStock = $parts->sum('stock');
        $totalStockValue = $parts->sum(function ($part) {
            return $part->stock * $part->cost;
        });
        
        $vehicleTypes = [
            'motorcycle' => 'Motor',
            'electric_bike' => 'Sepeda Listrik',
            'both' => 'Keduanya'
        ];
        
        $stockStatus = [
            'all' => 'Semua',
            'below_min' => 'Di Bawah Minimum',
            'out_of_stock' => 'Stok Habis',
            'available' => 'Tersedia'
        ];
        
        return view('inventory_transactions.report', compact(
            'parts',
            'totalStock',
            'totalStockValue',
            'vehicleTypes',
            'stockStatus'
        ));
    }
    
    /**
     * Generate inventory movement report.
     */
    public function movementReport(Request $request)
    {
        // Date range validation
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'part_id' => 'nullable|exists:parts,id',
        ]);
        
        $query = InventoryTransaction::with(['part', 'workOrder']);
        
        // Filter by part
        if ($request->has('part_id') && $request->part_id) {
            $query->where('part_id', $request->part_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('created_at')->get();
        
        // Calculate running balance for each transaction
        $runningBalance = 0;
        foreach ($transactions as $transaction) {
            $runningBalance += $transaction->quantity;
            $transaction->running_balance = $runningBalance;
        }
        
        $parts = Part::orderBy('name')->get();
        
        return view('inventory_transactions.movement', compact('transactions', 'parts'));
    }
}