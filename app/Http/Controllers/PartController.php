<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartController extends Controller
{
    /**
     * Display a listing of the parts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parts = Part::latest()->paginate(10);
        return view('parts.index', compact('parts'));
    }

    /**
     * Show the form for creating a new part.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('parts.create');
    }

    /**
     * Store a newly created part in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $part = Part::create($request->all());

        // Jika stok awal lebih besar dari 0, buat catatan transaksi inventori
        if ($part->stock > 0) {
            InventoryTransaction::create([
                'part_id' => $part->id,
                'quantity' => $part->stock,
                'transaction_type' => 'purchase',
                'notes' => 'Stok awal',
            ]);
        }

        return redirect()->route('parts.index')
            ->with('success', 'Suku cadang berhasil ditambahkan.');
    }

    /**
     * Display the specified part.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part)
    {
        $part->load('inventoryTransactions');
        return view('parts.show', compact('part'));
    }

    /**
     * Show the form for editing the specified part.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function edit(Part $part)
    {
        return view('parts.edit', compact('part'));
    }

    /**
     * Update the specified part in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek perubahan stok
        $oldStock = $part->stock;
        $newStock = $request->stock;
        
        $part->update($request->all());

        // Jika ada perubahan stok, buat catatan transaksi inventori
        if ($oldStock != $newStock) {
            $difference = $newStock - $oldStock;
            $transactionType = $difference > 0 ? 'adjustment' : 'adjustment';
            
            InventoryTransaction::create([
                'part_id' => $part->id,
                'quantity' => abs($difference),
                'transaction_type' => $transactionType,
                'notes' => 'Penyesuaian stok manual',
            ]);
        }

        return redirect()->route('parts.index')
            ->with('success', 'Informasi suku cadang berhasil diperbarui.');
    }

    /**
     * Remove the specified part from storage.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part)
    {
        $part->delete();

        return redirect()->route('parts.index')
            ->with('success', 'Suku cadang berhasil dihapus.');
    }
    
    /**
     * Display a listing of parts that are low on stock.
     *
     * @return \Illuminate\Http\Response
     */
    public function lowStock()
    {
        $lowStockParts = Part::whereRaw('stock <= min_stock')->paginate(10);
        
        return view('parts.low-stock', compact('lowStockParts'));
    }
}