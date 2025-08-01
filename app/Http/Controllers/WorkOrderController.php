<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Part;
use App\Models\WorkOrderService;
use App\Models\WorkOrderPart;
use App\Models\Payment;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the work orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with(['vehicle', 'mechanic', 'appointment', 'customer']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by mechanic if provided
        if ($request->has('mechanic_id') && $request->mechanic_id) {
            $query->where('mechanic_id', $request->mechanic_id);
        }

        // Filter by payment status if provided
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by work order number or customer name/phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('work_order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    // Search in customer relationship
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Sort by specified field and direction
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $workOrders = $query->paginate(15);

        $mechanics = User::where('role', 'mechanic')->get(['id', 'name']);

        return view('work_orders.index', compact('workOrders', 'mechanics'));
    }

    /**
     * Show the form for creating a new work order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $appointmentId = $request->query('appointment_id');
        $appointment = null;
        $vehicle = null;
        $customer = null;

        if ($appointmentId) {
            $appointment = Appointment::with(['vehicle', 'customer'])->findOrFail($appointmentId);
            $vehicle = $appointment->vehicle;
            $customer = $appointment->customer;
        }

        $vehicles = Vehicle::all();
        $customers = User::where('role', 'customer')->get();
        $mechanics = User::where('role', 'mechanic')->get();
        $services = Service::all();
        $parts = Part::where('stock', '>', 0)->get();

        return view('work_orders.create', compact(
            'appointment',
            'vehicle',
            'customer',
            'vehicles',
            'customers',
            'mechanics',
            'services',
            'parts'
        ));
    }

    /**
     * Store a newly created work order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'mechanic_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'services' => 'nullable|array|min:1',
            'services.*.service_id' => 'required_with:services|exists:services,id',
            'services.*.quantity' => 'required_with:services|integer|min:1',
            'services.*.price' => 'required_with:services|numeric|min:0',
            'services.*.notes' => 'nullable|string',
            'parts' => 'nullable|array|min:1',
            'parts.*.part_id' => 'required_with:parts|exists:parts,id',
            'parts.*.quantity' => 'required_with:parts|integer|min:1',
            'parts.*.price' => 'required_with:parts|numeric|min:0',
            'diagnosis' => 'nullable|string',
        ]);

        // Add custom validation to ensure at least one service or part is provided
        $validator->after(function ($validator) use ($request) {
            $hasServices = $request->has('services') && is_array($request->services) && !empty($request->services);
            $hasParts = $request->has('parts') && is_array($request->parts) && !empty($request->parts);

            if (!$hasServices && !$hasParts) {
                $validator->errors()->add('services', 'Harap tambahkan minimal satu layanan atau spare part.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Harap periksa kembali data yang diinput.');
        }

        DB::beginTransaction();

        try {
            // Generate work order number
            $lastWorkOrder = WorkOrder::orderBy('id', 'desc')->first();
            $nextNumber = $lastWorkOrder ? $lastWorkOrder->id + 1 : 1;
            $workOrderNumber = 'WO-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Prepare work order data
            $workOrderData = [
                'work_order_number' => $workOrderNumber,
                'appointment_id' => $request->appointment_id,
                'customer_id' => $request->customer_id,
                'vehicle_id' => $request->vehicle_id,
                'mechanic_id' => $request->mechanic_id,
                'status' => 'pending',
                'diagnosis' => $request->diagnosis,
                'payment_status' => 'unpaid',
                'total_amount' => 0, // Will be calculated later
            ];

            // Handle customer data - prioritize customer_id if provided
            if ($request->customer_id) {
                $customer = User::find($request->customer_id);
                $workOrderData['customer_id'] = $request->customer_id;
                $workOrderData['customer_name'] = $customer->name;
                $workOrderData['customer_phone'] = $customer->phone;
            } else {
                // Store customer name and phone for walk-in customers
                $workOrderData['customer_id'] = null;
                $workOrderData['customer_name'] = $request->customer_name;
                $workOrderData['customer_phone'] = $request->customer_phone;
            }

            // Create work order
            $workOrder = WorkOrder::create($workOrderData);

            $totalAmount = 0;

            // Add services to work order
            if ($request->has('services') && is_array($request->services)) {
                foreach ($request->services as $serviceData) {
                    if (!empty($serviceData['service_id'])) {
                        $serviceTotal = $serviceData['quantity'] * $serviceData['price'];
                        $totalAmount += $serviceTotal;

                        WorkOrderService::create([
                            'work_order_id' => $workOrder->id,
                            'service_id' => $serviceData['service_id'],
                            'quantity' => $serviceData['quantity'],
                            'price' => $serviceData['price'],
                            'notes' => $serviceData['notes'] ?? null,
                        ]);
                    }
                }
            }

            // Add parts to work order and update inventory
            if ($request->has('parts') && is_array($request->parts)) {
                foreach ($request->parts as $partData) {
                    if (!empty($partData['part_id'])) {
                        // Check if enough stock is available
                        $part = Part::findOrFail($partData['part_id']);
                        if ($part->stock < $partData['quantity']) {
                            throw new \Exception("Stok tidak mencukupi untuk part: {$part->name}. Stok tersedia: {$part->stock}");
                        }

                        $partTotal = $partData['quantity'] * $partData['price'];
                        $totalAmount += $partTotal;

                        // Create work order part
                        WorkOrderPart::create([
                            'work_order_id' => $workOrder->id,
                            'part_id' => $partData['part_id'],
                            'quantity' => $partData['quantity'],
                            'price' => $partData['price'],
                        ]);

                        // Update inventory stock
                        $part->stock -= $partData['quantity'];
                        $part->save();

                        // Record inventory transaction
                        InventoryTransaction::create([
                            'part_id' => $partData['part_id'],
                            'work_order_id' => $workOrder->id,
                            'quantity' => -1 * $partData['quantity'], // Negative for sales
                            'transaction_type' => 'sales',
                            'notes' => "Digunakan untuk Work Order #{$workOrder->work_order_number}",
                        ]);
                    }
                }
            }

            // Update total amount
            $workOrder->total_amount = $totalAmount;
            $workOrder->save();

            // Update appointment status if work order was created from an appointment
            if ($request->appointment_id) {
                $appointment = Appointment::find($request->appointment_id);
                if ($appointment) {
                    $appointment->status = 'in_progress';
                    $appointment->save();
                }
            }

            DB::commit();

            return redirect()->route('work-orders.show', $workOrder->id)
                ->with('success', 'Work order berhasil dibuat dengan nomor: ' . $workOrder->work_order_number);
        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error creating work order: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal membuat work order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified work order.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function show(WorkOrder $workOrder)
    {
        try {
            $workOrder->load([
                'customer',
                'vehicle',
                'mechanic',
                'appointment',
                'services.service',
                'parts.part',
                'payments',
                'feedback'
            ]);

            return view('work_orders.show', compact('workOrder'));
        } catch (\Exception $e) {
            \Log::error('Error loading work order: ' . $e->getMessage(), [
                'work_order_id' => $workOrder->id ?? 'unknown'
            ]);

            return redirect()->route('work-orders.index')
                ->with('error', 'Work order tidak ditemukan atau terjadi kesalahan.');
        }
    }

    /**
     * Show the form for editing the specified work order.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrder $workOrder)
    {
        $workOrder->load([
            'customer',
            'vehicle',
            'mechanic',
            'appointment',
            'services.service',
            'parts.part'
        ]);

        $vehicles = Vehicle::all();
        $customers = User::where('role', 'customer')->get();
        $mechanics = User::where('role', 'mechanic')->get();
        $services = Service::all();
        $parts = Part::where('stock', '>', 0)
            ->orWhereIn('id', $workOrder->parts->pluck('part_id'))
            ->get();

        return view('work_orders.edit', compact(
            'workOrder',
            'vehicles',
            'customers',
            'mechanics',
            'services',
            'parts'
        ));
    }

    /**
     * Update the specified work order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_phone' => 'required_without:customer_id|string|max:15',
            'mechanic_id' => 'required|exists:users,id',
            'diagnosis' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.price' => 'required|numeric|min:0',
            'parts' => 'nullable|array',
            'parts.*.part_id' => 'required|exists:parts,id',
            'parts.*.quantity' => 'required|integer|min:1',
            'parts.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Prepare update data
            $updateData = [
                'mechanic_id' => $request->mechanic_id,
                'diagnosis' => $request->diagnosis,
                'status' => $request->status,
            ];

            // Handle customer data
            if ($request->customer_id) {
                $updateData['customer_id'] = $request->customer_id;
                $updateData['customer_name'] = null; // Clear fallback data
                $updateData['customer_phone'] = null; // Clear fallback data
            } else {
                $updateData['customer_id'] = null;
                $updateData['customer_name'] = $request->customer_name;
                $updateData['customer_phone'] = $request->customer_phone;
            }

            // Update work order details
            $workOrder->update($updateData);

            // Handle start and end times based on status
            $oldStatus = $workOrder->getOriginal('status');
            $newStatus = $request->status;

            if ($oldStatus != 'in_progress' && $newStatus == 'in_progress') {
                $workOrder->start_time = now();
            }

            if ($oldStatus != 'completed' && $newStatus == 'completed') {
                $workOrder->end_time = now();

                // Update appointment status if exists
                if ($workOrder->appointment_id) {
                    $appointment = Appointment::find($workOrder->appointment_id);
                    if ($appointment) {
                        $appointment->status = 'completed';
                        $appointment->save();
                    }
                }
            }

            $workOrder->save();

            // Update services
            $existingServices = $workOrder->services->pluck('id', 'id')->toArray();

            if ($request->has('services')) {
                foreach ($request->services as $serviceData) {
                    if (isset($serviceData['id'])) {
                        // Update existing service
                        $service = WorkOrderService::find($serviceData['id']);
                        if ($service) {
                            $service->update([
                                'service_id' => $serviceData['service_id'],
                                'quantity' => $serviceData['quantity'],
                                'price' => $serviceData['price'],
                                'notes' => $serviceData['notes'] ?? null,
                            ]);
                            unset($existingServices[$service->id]);
                        }
                    } else {
                        // Add new service
                        WorkOrderService::create([
                            'work_order_id' => $workOrder->id,
                            'service_id' => $serviceData['service_id'],
                            'quantity' => $serviceData['quantity'],
                            'price' => $serviceData['price'],
                            'notes' => $serviceData['notes'] ?? null,
                        ]);
                    }
                }
            }

            // Remove services that were deleted
            if (!empty($existingServices)) {
                WorkOrderService::whereIn('id', array_keys($existingServices))->delete();
            }

            // Update parts with proper inventory handling
            $existingParts = $workOrder->parts->keyBy('id')->toArray();
            $updatedParts = [];

            if ($request->has('parts')) {
                foreach ($request->parts as $partData) {
                    if (isset($partData['id'])) {
                        // Update existing part
                        $workOrderPart = WorkOrderPart::find($partData['id']);
                        if ($workOrderPart) {
                            $originalQuantity = $workOrderPart->quantity;
                            $newQuantity = $partData['quantity'];
                            $quantityDiff = $newQuantity - $originalQuantity;

                            // If quantity changed, update inventory
                            if ($quantityDiff != 0) {
                                $part = Part::find($partData['part_id']);

                                // If increasing quantity, check if we have enough stock
                                if ($quantityDiff > 0 && $part->stock < $quantityDiff) {
                                    throw new \Exception("Not enough stock for part: {$part->name}");
                                }

                                // Update part stock
                                $part->stock -= $quantityDiff;
                                $part->save();

                                // Record inventory transaction
                                InventoryTransaction::create([
                                    'part_id' => $partData['part_id'],
                                    'work_order_id' => $workOrder->id,
                                    'quantity' => -1 * $quantityDiff,
                                    'transaction_type' => $quantityDiff > 0 ? 'sales' : 'return',
                                    'notes' => "Adjusted in Work Order #{$workOrder->work_order_number}",
                                ]);
                            }

                            $workOrderPart->update([
                                'quantity' => $newQuantity,
                                'price' => $partData['price'],
                            ]);

                            $updatedParts[$workOrderPart->id] = true;
                        }
                    } else {
                        // Add new part
                        $part = Part::findOrFail($partData['part_id']);
                        if ($part->stock < $partData['quantity']) {
                            throw new \Exception("Not enough stock for part: {$part->name}");
                        }

                        // Create work order part
                        $workOrderPart = WorkOrderPart::create([
                            'work_order_id' => $workOrder->id,
                            'part_id' => $partData['part_id'],
                            'quantity' => $partData['quantity'],
                            'price' => $partData['price'],
                        ]);

                        // Update inventory stock
                        $part->stock -= $partData['quantity'];
                        $part->save();

                        // Record inventory transaction
                        InventoryTransaction::create([
                            'part_id' => $partData['part_id'],
                            'work_order_id' => $workOrder->id,
                            'quantity' => -1 * $partData['quantity'],
                            'transaction_type' => 'sales',
                            'notes' => "Added to Work Order #{$workOrder->work_order_number}",
                        ]);

                        $updatedParts[$workOrderPart->id] = true;
                    }
                }
            }

            // Handle removed parts
            foreach ($workOrder->parts as $existingPart) {
                if (!isset($updatedParts[$existingPart->id])) {
                    // Return parts to inventory
                    $part = Part::find($existingPart->part_id);
                    $part->stock += $existingPart->quantity;
                    $part->save();

                    // Record inventory transaction
                    InventoryTransaction::create([
                        'part_id' => $existingPart->part_id,
                        'work_order_id' => $workOrder->id,
                        'quantity' => $existingPart->quantity,
                        'transaction_type' => 'return',
                        'notes' => "Removed from Work Order #{$workOrder->work_order_number}",
                    ]);

                    // Delete the work order part
                    $existingPart->delete();
                }
            }

            // Calculate and update total using the model method
            $workOrder->updateTotal();

            DB::commit();

            return redirect()->route('work-orders.show', $workOrder->id)
                ->with('success', 'Work order updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update work order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified work order from storage.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrder $workOrder)
    {
        // Only allow deletion of pending work orders
        if ($workOrder->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending work orders can be deleted.');
        }

        DB::beginTransaction();

        try {
            // Return parts to inventory
            foreach ($workOrder->parts as $workOrderPart) {
                $part = Part::find($workOrderPart->part_id);
                $part->stock += $workOrderPart->quantity;
                $part->save();

                // Record inventory transaction
                InventoryTransaction::create([
                    'part_id' => $workOrderPart->part_id,
                    'work_order_id' => null,
                    'quantity' => $workOrderPart->quantity,
                    'transaction_type' => 'return',
                    'notes' => "Returned from deleted Work Order #{$workOrder->work_order_number}",
                ]);
            }

            // Update appointment status if work order was created from an appointment
            if ($workOrder->appointment_id) {
                $appointment = Appointment::find($workOrder->appointment_id);
                if ($appointment) {
                    $appointment->status = 'confirmed';
                    $appointment->save();
                }
            }

            // Delete the work order (will cascade delete related records)
            $workOrder->delete();

            DB::commit();

            return redirect()->route('work-orders.index')
                ->with('success', 'Work order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to delete work order: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified work order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $oldStatus = $workOrder->status;
        $newStatus = $request->status;

        // Update work order status
        $workOrder->status = $newStatus;

        // Handle start and end times based on status
        if ($oldStatus != 'in_progress' && $newStatus == 'in_progress') {
            $workOrder->start_time = now();
        }

        if ($oldStatus != 'completed' && $newStatus == 'completed') {
            $workOrder->end_time = now();

            // Update appointment status if exists
            if ($workOrder->appointment_id) {
                $appointment = Appointment::find($workOrder->appointment_id);
                if ($appointment) {
                    $appointment->status = 'completed';
                    $appointment->save();
                }
            }
        }

        // If cancelled, return parts to inventory
        if ($newStatus == 'cancelled') {
            DB::beginTransaction();

            try {
                foreach ($workOrder->parts as $workOrderPart) {
                    $part = Part::find($workOrderPart->part_id);
                    $part->stock += $workOrderPart->quantity;
                    $part->save();

                    // Record inventory transaction
                    InventoryTransaction::create([
                        'part_id' => $workOrderPart->part_id,
                        'work_order_id' => $workOrder->id,
                        'quantity' => $workOrderPart->quantity,
                        'transaction_type' => 'return',
                        'notes' => "Returned from cancelled Work Order #{$workOrder->work_order_number}",
                    ]);
                }

                // Update appointment status if exists
                if ($workOrder->appointment_id) {
                    $appointment = Appointment::find($workOrder->appointment_id);
                    if ($appointment) {
                        $appointment->status = 'cancelled';
                        $appointment->save();
                    }
                }

                $workOrder->save();
                DB::commit();

                return redirect()->back()
                    ->with('success', 'Work order status updated to ' . $newStatus);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'Failed to update work order status: ' . $e->getMessage());
            }
        }

        $workOrder->save();

        return redirect()->back()
            ->with('success', 'Work order status updated to ' . $newStatus);
    }

    /**
     * Show the form for adding a payment to the work order.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function createPayment(WorkOrder $workOrder)
    {
        return view('work_orders.payment', compact('workOrder'));
    }

    /**
     * Store a newly created payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function storePayment(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer,qris,credit_card,other',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Use the model's remaining balance accessor
        $remainingBalance = $workOrder->remaining_balance;
        $paymentAmount = $request->amount;

        if ($paymentAmount > $remainingBalance) {
            return redirect()->back()
                ->with('error', "Payment amount exceeds the remaining balance of " . number_format($remainingBalance, 2))
                ->withInput();
        }

        // Create payment
        $payment = Payment::create([
            'work_order_id' => $workOrder->id,
            'amount' => $paymentAmount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ]);

        // Update work order payment status using model method
        $workOrder->updatePaymentStatus();

        return redirect()->route('work-orders.show', $workOrder->id)
            ->with('success', 'Payment added successfully.');
    }

    /**
     * Generate an invoice for the work order.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function invoice(WorkOrder $workOrder)
    {
        $workOrder->load([
            'customer',
            'vehicle',
            'mechanic',
            'services.service',
            'parts.part',
            'payments'
        ]);

        return view('work_orders.invoice', compact('workOrder'));
    }

    /**
     * Generate a printable service receipt for the work order.
     *
     * @param  \App\Models\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function receipt(WorkOrder $workOrder)
    {
        $workOrder->load([
            'customer',
            'vehicle',
            'mechanic',
            'services.service',
            'parts.part',
            'payments'
        ]);

        return view('work_orders.receipt', compact('workOrder'));
    }

    /**
     * Show the customer feedback form for the work order.
     *
     * @param  string  $workOrderNumber
     * @return \Illuminate\Http\Response
     */
    public function feedbackForm($workOrderNumber)
    {
        $workOrder = WorkOrder::where('work_order_number', $workOrderNumber)
            ->where('status', 'completed')
            ->with(['feedback'])
            ->firstOrFail();

        // Check if feedback already exists
        if ($workOrder->feedback) {
            return view('work_orders.feedback_submitted');
        }

        return view('work_orders.feedback_form', compact('workOrder'));
    }

    /**
     * Store customer feedback for the work order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $workOrderNumber
     * @return \Illuminate\Http\Response
     */
    public function storeFeedback(Request $request, $workOrderNumber)
    {
        $workOrder = WorkOrder::where('work_order_number', $workOrderNumber)
            ->where('status', 'completed')
            ->firstOrFail();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        // Create feedback using the relationship
        $feedback = $workOrder->feedback()->create([
            'customer_name' => $request->customer_name,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_public' => $request->has('is_public') ? true : false,
        ]);

        return view('work_orders.feedback_thank_you');
    }
}
