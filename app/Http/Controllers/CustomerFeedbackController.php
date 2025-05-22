<?php

namespace App\Http\Controllers;

use App\Models\CustomerFeedback;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of the customer feedback.
     */
    public function index(Request $request)
    {
        $query = CustomerFeedback::with('workOrder');
        
        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }
        
        // Filter by visibility
        if ($request->has('visibility')) {
            if ($request->visibility == 'public') {
                $query->where('is_public', true);
            } elseif ($request->visibility == 'private') {
                $query->where('is_public', false);
            }
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $feedback = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('customer_feedback.index', compact('feedback'));
    }

    /**
     * Show the form for creating a new customer feedback.
     */
    public function create(WorkOrder $workOrder = null)
    {
        return view('customer_feedback.create', compact('workOrder'));
    }

    /**
     * Store a newly created customer feedback in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        // Check if feedback already exists for this work order
        $exists = CustomerFeedback::where('work_order_id', $validated['work_order_id'])->exists();
        if ($exists) {
            return back()->withErrors(['work_order_id' => 'Feedback sudah diberikan untuk work order ini.'])
                ->withInput();
        }

        $feedback = CustomerFeedback::create($validated);

        return redirect()->route('customer-feedback.show', $feedback)
            ->with('success', 'Terima kasih atas feedback Anda!');
    }

    /**
     * Display the specified customer feedback.
     */
    public function show(CustomerFeedback $customerFeedback)
    {
        $customerFeedback->load('workOrder');
        return view('customer_feedback.show', compact('customerFeedback'));
    }

    /**
     * Show the form for editing the specified customer feedback.
     */
    public function edit(CustomerFeedback $customerFeedback)
    {
        $customerFeedback->load('workOrder');
        return view('customer_feedback.edit', compact('customerFeedback'));
    }

    /**
     * Update the specified customer feedback in storage.
     */
    public function update(Request $request, CustomerFeedback $customerFeedback)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $customerFeedback->update($validated);

        return redirect()->route('customer-feedback.show', $customerFeedback)
            ->with('success', 'Feedback berhasil diperbarui.');
    }

    /**
     * Remove the specified customer feedback from storage.
     */
    public function destroy(CustomerFeedback $customerFeedback)
    {
        $customerFeedback->delete();

        return redirect()->route('customer-feedback.index')
            ->with('success', 'Feedback berhasil dihapus.');
    }
    
    /**
     * Show public feedback for testimonials page.
     */
    public function testimonials()
    {
        $testimonials = CustomerFeedback::public()
            ->with('workOrder')
            ->where('rating', '>=', 4) // Only show good feedback (4-5 stars)
            ->latest()
            ->paginate(9);
            
        return view('customer_feedback.testimonials', compact('testimonials'));
    }
    
    /**
     * Show the public feedback form for customers.
     */
    public function publicForm($trackingCode)
    {
        $workOrder = WorkOrder::whereHas('appointment', function ($query) use ($trackingCode) {
            $query->where('tracking_code', $trackingCode);
        })->where('status', 'completed')->first();
        
        if (!$workOrder) {
            return redirect()->route('home')
                ->with('error', 'Work order tidak ditemukan atau belum selesai.');
        }
        
        // Check if feedback already exists
        $exists = CustomerFeedback::where('work_order_id', $workOrder->id)->exists();
        if ($exists) {
            return redirect()->route('home')
                ->with('info', 'Anda sudah memberikan feedback untuk servis ini.');
        }
        
        return view('customer_feedback.public_form', compact('workOrder', 'trackingCode'));
    }
    
    /**
     * Store feedback from public form.
     */
    public function storePublic(Request $request, $trackingCode)
    {
        $workOrder = WorkOrder::whereHas('appointment', function ($query) use ($trackingCode) {
            $query->where('tracking_code', $trackingCode);
        })->where('status', 'completed')->first();
        
        if (!$workOrder) {
            return redirect()->route('home')
                ->with('error', 'Work order tidak ditemukan atau belum selesai.');
        }
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ]);
        
        // Add work order ID to validated data
        $validated['work_order_id'] = $workOrder->id;
        
        // Create feedback
        CustomerFeedback::create($validated);
        
        return redirect()->route('home')
            ->with('success', 'Terima kasih atas feedback Anda!');
    }
}