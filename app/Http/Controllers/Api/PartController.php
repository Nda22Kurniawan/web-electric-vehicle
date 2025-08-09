<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Part;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartController extends Controller
{
    /**
     * Display a listing of parts
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $vehicleType = $request->get('vehicle_type'); // Filter by vehicle type
        $lowStock = $request->get('low_stock'); // Filter low stock items
        
        $query = Part::query();
        
        // Filter by vehicle type
        if ($vehicleType && in_array($vehicleType, ['motorcycle', 'electric_bike'])) {
            $query->where(function($q) use ($vehicleType) {
                $q->where('vehicle_type', $vehicleType)
                  ->orWhere('vehicle_type', 'both');
            });
        }
        
        // Filter low stock items
        if ($lowStock === 'true') {
            $query->whereRaw('stock <= min_stock');
        }
        
        $parts = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Parts retrieved successfully',
            'data' => $parts->items(),
            'meta' => [
                'current_page' => $parts->currentPage(),
                'last_page' => $parts->lastPage(),
                'per_page' => $parts->perPage(),
                'total' => $parts->total(),
            ]
        ]);
    }

    /**
     * Store a newly created part
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'part_number' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'vehicle_type' => 'required|in:motorcycle,electric_bike,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $part = Part::create($request->only([
            'name', 'description', 'part_number', 'price', 'cost', 
            'stock', 'min_stock', 'vehicle_type'
        ]));

        // Jika stok awal lebih besar dari 0, buat catatan transaksi inventori
        if ($part->stock > 0) {
            InventoryTransaction::create([
                'part_id' => $part->id,
                'quantity' => $part->stock,
                'transaction_type' => 'purchase',
                'notes' => 'Initial stock',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Part created successfully',
            'data' => $part
        ], 201);
    }

    /**
     * Display the specified part
     */
    public function show(Part $part)
    {
        $part->load('inventoryTransactions');

        return response()->json([
            'success' => true,
            'message' => 'Part retrieved successfully',
            'data' => $part
        ]);
    }

    /**
     * Update the specified part
     */
    public function update(Request $request, Part $part)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'part_number' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'vehicle_type' => 'required|in:motorcycle,electric_bike,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek perubahan stok
        $oldStock = $part->stock;
        $newStock = $request->stock;
        
        $part->update($request->only([
            'name', 'description', 'part_number', 'price', 'cost', 
            'stock', 'min_stock', 'vehicle_type'
        ]));

        // Jika ada perubahan stok, buat catatan transaksi inventori
        if ($oldStock != $newStock) {
            $difference = $newStock - $oldStock;
            
            InventoryTransaction::create([
                'part_id' => $part->id,
                'quantity' => abs($difference),
                'transaction_type' => 'adjustment',
                'notes' => 'Manual stock adjustment - ' . ($difference > 0 ? 'increase' : 'decrease') . ' by ' . abs($difference),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Part updated successfully',
            'data' => $part->fresh()
        ]);
    }

    /**
     * Remove the specified part
     */
    public function destroy(Part $part)
    {
        // Check if part is used in any work orders
        if ($part->workOrderParts()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete part that is used in work orders'
            ], 400);
        }

        $part->delete();

        return response()->json([
            'success' => true,
            'message' => 'Part deleted successfully'
        ]);
    }

    /**
     * Display parts that are low on stock
     */
    public function lowStock(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $lowStockParts = Part::whereRaw('stock <= min_stock')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Low stock parts retrieved successfully',
            'data' => $lowStockParts->items(),
            'meta' => [
                'current_page' => $lowStockParts->currentPage(),
                'last_page' => $lowStockParts->lastPage(),
                'per_page' => $lowStockParts->perPage(),
                'total' => $lowStockParts->total(),
            ]
        ]);
    }

    /**
     * Search parts by name, part number, or description
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $perPage = $request->get('per_page', 15);
        
        if (empty($query)) {
            return $this->index($request);
        }

        $parts = Part::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('part_number', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->latest()
        ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => $parts->items(),
            'meta' => [
                'current_page' => $parts->currentPage(),
                'last_page' => $parts->lastPage(),
                'per_page' => $parts->perPage(),
                'total' => $parts->total(),
                'search_query' => $query
            ]
        ]);
    }

    /**
     * Adjust part stock (increase/decrease)
     */
    public function adjustStock(Request $request, Part $part)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|not_in:0',
            'type' => 'required|in:increase,decrease',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $quantity = $request->quantity;
        $type = $request->type;
        $notes = $request->notes ?? '';

        // Calculate new stock
        $newStock = $type === 'increase' 
            ? $part->stock + $quantity 
            : $part->stock - $quantity;

        // Prevent negative stock
        if ($newStock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot decrease stock below zero. Current stock: ' . $part->stock
            ], 400);
        }

        // Update part stock
        $part->update(['stock' => $newStock]);

        // Create inventory transaction
        InventoryTransaction::create([
            'part_id' => $part->id,
            'quantity' => $quantity,
            'transaction_type' => $type === 'increase' ? 'purchase' : 'usage',
            'notes' => $notes ?: "Stock {$type} by {$quantity}",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stock {$type}d successfully",
            'data' => [
                'part' => $part->fresh(),
                'old_stock' => $part->stock,
                'new_stock' => $newStock,
                'adjustment' => ($type === 'increase' ? '+' : '-') . $quantity
            ]
        ]);
    }

    /**
     * Get part inventory transactions
     */
    public function getTransactions(Request $request, Part $part)
    {
        $perPage = $request->get('per_page', 15);
        
        $transactions = $part->inventoryTransactions()
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Part transactions retrieved successfully',
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }
}