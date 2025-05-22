<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderPart;
use App\Models\WorkOrder;
use App\Models\Part;
use Illuminate\Http\Request;

class WorkOrderPartController extends Controller
{
    /**
     * Display a listing of the parts for a work order.
     */
    public function index(WorkOrder $workOrder)
    {
        $parts = $workOrder->parts()->with('part')->get();
        return view('work_order_parts.index', compact('workOrder', 'parts'));
    }

    /**
     * Show the form for creating a new work order part.
     */
    public function create(WorkOrder $workOrder)
    {
        $parts = Part::where('stock', '>', 0)->get();
        return view('work_order_parts.create', compact('workOrder', 'parts'));
    }

    /**
     * Store a newly created work order part in storage.
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Check if there's enough stock
        $part = Part::findOrFail($validated['part_id']);
        if ($part->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi. Tersedia: ' . $part->stock])
                ->withInput();
        }

        $workOrderPart = $workOrder->parts()->create($validated);
        
        // The inventory transaction is handled by the model observer

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Suku cadang berhasil ditambahkan ke work order.');
    }

    /**
     * Show the form for editing the specified work order part.
     */
    public function edit(WorkOrder $workOrder, WorkOrderPart $part)
    {
        $parts = Part::all();
        return view('work_order_parts.edit', compact('workOrder', 'part', 'parts'));
    }

    /**
     * Update the specified work order part in storage.
     */
    public function update(Request $request, WorkOrder $workOrder, WorkOrderPart $part)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Check if part ID has changed
        if ($validated['part_id'] != $part->part_id) {
            return back()->withErrors(['part_id' => 'Tidak dapat mengubah jenis suku cadang. Hapus dan tambahkan yang baru.'])
                ->withInput();
        }

        // Check stock if quantity has increased
        if ($validated['quantity'] > $part->quantity) {
            $additionalQuantity = $validated['quantity'] - $part->quantity;
            if ($part->part->stock < $additionalQuantity) {
                return back()->withErrors(['quantity' => 'Stok tidak mencukupi. Tersedia: ' . $part->part->stock])
                    ->withInput();
            }
        }

        $part->update($validated);
        
        // Inventory transactions are handled by the model observer

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Suku cadang work order berhasil diperbarui.');
    }

    /**
     * Remove the specified work order part from storage.
     */
    public function destroy(WorkOrder $workOrder, WorkOrderPart $part)
    {
        $part->delete();
        
        // Inventory return transaction is handled by the model observer

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Suku cadang berhasil dihapus dari work order.');
    }
}