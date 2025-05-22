<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments for a work order.
     */
    public function index(WorkOrder $workOrder)
    {
        $payments = $workOrder->payments()->orderBy('payment_date', 'desc')->get();
        return view('payments.index', compact('workOrder', 'payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(WorkOrder $workOrder)
    {
        $paymentMethods = Payment::paymentMethods();
        $remainingBalance = $workOrder->remaining_balance;
        
        return view('payments.create', compact('workOrder', 'paymentMethods', 'remainingBalance'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:' . implode(',', array_keys(Payment::paymentMethods())),
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Check if payment amount exceeds remaining balance
        if ($validated['amount'] > $workOrder->remaining_balance) {
            return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi sisa tagihan (' . number_format($workOrder->remaining_balance, 0, ',', '.') . ')'])
                ->withInput();
        }

        $payment = $workOrder->payments()->create($validated);
        
        // Work order payment status is updated by the model observer
        
        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    /**
     * Display the specified payment.
     */
    public function show(WorkOrder $workOrder, Payment $payment)
    {
        return view('payments.show', compact('workOrder', 'payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(WorkOrder $workOrder, Payment $payment)
    {
        $paymentMethods = Payment::paymentMethods();
        return view('payments.edit', compact('workOrder', 'payment', 'paymentMethods'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, WorkOrder $workOrder, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:' . implode(',', array_keys(Payment::paymentMethods())),
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Check if new amount plus other payments exceeds total amount
        $otherPaymentsTotal = $workOrder->payments()
            ->where('id', '!=', $payment->id)
            ->sum('amount');
            
        if ($validated['amount'] + $otherPaymentsTotal > $workOrder->total_amount) {
            return back()->withErrors(['amount' => 'Total pembayaran melebihi total tagihan work order.'])
                ->withInput();
        }

        $payment->update($validated);
        
        // Work order payment status is updated by the model observer

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(WorkOrder $workOrder, Payment $payment)
    {
        $payment->delete();
        
        // Work order payment status is updated by the model observer

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
    
    /**
     * Generate payment receipt.
     */
    public function receipt(WorkOrder $workOrder, Payment $payment)
    {
        // Generate PDF receipt
        $data = [
            'workOrder' => $workOrder,
            'payment' => $payment,
            'vehicle' => $workOrder->vehicle,
        ];
        
        $pdf = Pdf::loadView('payments.receipt', $data);
        
        return $pdf->download('receipt-' . $payment->id . '.pdf');
    }
}