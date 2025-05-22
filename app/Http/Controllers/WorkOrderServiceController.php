<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderService;
use App\Models\WorkOrder;
use App\Models\Service;
use Illuminate\Http\Request;

class WorkOrderServiceController extends Controller
{
    /**
     * Display a listing of the services for a work order.
     */
    public function index(WorkOrder $workOrder)
    {
        $services = $workOrder->services()->with('service')->get();
        return view('work_order_services.index', compact('workOrder', 'services'));
    }

    /**
     * Show the form for creating a new work order service.
     */
    public function create(WorkOrder $workOrder)
    {
        $services = Service::all();
        return view('work_order_services.create', compact('workOrder', 'services'));
    }

    /**
     * Store a newly created work order service in storage.
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $workOrderService = $workOrder->services()->create($validated);
        
        // Update work order total after adding service
        $workOrder->updateTotal();
        $workOrder->updatePaymentStatus();

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Layanan berhasil ditambahkan ke work order.');
    }

    /**
     * Show the form for editing the specified work order service.
     */
    public function edit(WorkOrder $workOrder, WorkOrderService $service)
    {
        $services = Service::all();
        return view('work_order_services.edit', compact('workOrder', 'service', 'services'));
    }

    /**
     * Update the specified work order service in storage.
     */
    public function update(Request $request, WorkOrder $workOrder, WorkOrderService $service)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $service->update($validated);
        
        // Update work order total after updating service
        $workOrder->updateTotal();
        $workOrder->updatePaymentStatus();

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Layanan work order berhasil diperbarui.');
    }

    /**
     * Remove the specified work order service from storage.
     */
    public function destroy(WorkOrder $workOrder, WorkOrderService $service)
    {
        $service->delete();
        
        // Update work order total after removing service
        $workOrder->updateTotal();
        $workOrder->updatePaymentStatus();

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Layanan berhasil dihapus dari work order.');
    }
}