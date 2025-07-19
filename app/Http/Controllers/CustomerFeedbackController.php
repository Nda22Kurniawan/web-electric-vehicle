<?php

namespace App\Http\Controllers;

use App\Models\CustomerFeedback;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of the customer feedback.
     */
    public function index(Request $request)
    {
        $query = CustomerFeedback::with(['workOrder', 'customer']);
        
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
        $customers = User::where('role', 'customer')->get(); // Assuming you have a role field
        return view('customer_feedback.create', compact('workOrder', 'customers'));
    }

    /**
     * Store a newly created customer feedback in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'work_order_id' => 'required|exists:work_orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ];

        // Check if using existing customer or new customer data
        if ($request->filled('customer_id')) {
            $validationRules['customer_id'] = 'required|exists:users,id';
        } else {
            $validationRules['customer_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($validationRules);

        // Check if feedback already exists for this work order
        $exists = CustomerFeedback::where('work_order_id', $validated['work_order_id'])->exists();
        if ($exists) {
            return back()->withErrors(['work_order_id' => 'Feedback sudah diberikan untuk work order ini.'])
                ->withInput();
        }

        // Prepare data for creation
        $feedbackData = [
            'work_order_id' => $validated['work_order_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ];

        // Add customer data based on input type
        if ($request->filled('customer_id')) {
            $feedbackData['customer_id'] = $validated['customer_id'];
        } else {
            $feedbackData['customer_name'] = $validated['customer_name'];
        }

        $feedback = CustomerFeedback::create($feedbackData);

        return redirect()->route('customer-feedback.show', $feedback)
            ->with('success', 'Terima kasih atas feedback Anda!');
    }

    /**
     * Display the specified customer feedback.
     */
    public function show(CustomerFeedback $customerFeedback)
    {
        $customerFeedback->load(['workOrder', 'customer']);
        return view('customer_feedback.show', compact('customerFeedback'));
    }

    /**
     * Show the form for editing the specified customer feedback.
     */
    public function edit(CustomerFeedback $customerFeedback)
    {
        $customerFeedback->load(['workOrder', 'customer']);
        $customers = User::where('role', 'customer')->get(); // Assuming you have a role field
        return view('customer_feedback.edit', compact('customerFeedback', 'customers'));
    }

    /**
     * Update the specified customer feedback in storage.
     */
    public function update(Request $request, CustomerFeedback $customerFeedback)
    {
        $validationRules = [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ];

        // Check if using existing customer or updating customer data
        if ($request->filled('customer_id')) {
            $validationRules['customer_id'] = 'required|exists:users,id';
        } else {
            $validationRules['customer_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($validationRules);

        // Prepare update data
        $updateData = [
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ];

        // Handle customer data update
        if ($request->filled('customer_id')) {
            $updateData['customer_id'] = $validated['customer_id'];
            // Clear old customer data if switching to registered customer
            $updateData['customer_name'] = null;
        } else {
            $updateData['customer_id'] = null;
            $updateData['customer_name'] = $validated['customer_name'];
        }

        $customerFeedback->update($updateData);

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
            ->with(['workOrder', 'customer'])
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
        
        // Check if feedback already exists
        $exists = CustomerFeedback::where('work_order_id', $workOrder->id)->exists();
        if ($exists) {
            return redirect()->route('home')
                ->with('info', 'Anda sudah memberikan feedback untuk servis ini.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_public' => 'boolean',
        ]);
        
        // Create feedback data for public form (no customer_id, only customer_name)
        $feedbackData = [
            'work_order_id' => $workOrder->id,
            'customer_name' => $validated['customer_name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ];
        
        // Create feedback
        CustomerFeedback::create($feedbackData);
        
        return redirect()->route('home')
            ->with('success', 'Terima kasih atas feedback Anda!');
    }

    /**
     * Get feedback statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_feedback' => CustomerFeedback::count(),
            'public_feedback' => CustomerFeedback::where('is_public', true)->count(),
            'average_rating' => round(CustomerFeedback::avg('rating'), 1),
            'rating_distribution' => [
                5 => CustomerFeedback::where('rating', 5)->count(),
                4 => CustomerFeedback::where('rating', 4)->count(),
                3 => CustomerFeedback::where('rating', 3)->count(),
                2 => CustomerFeedback::where('rating', 2)->count(),
                1 => CustomerFeedback::where('rating', 1)->count(),
            ],
            'recent_feedback' => CustomerFeedback::with(['workOrder', 'customer'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Toggle feedback visibility (public/private).
     */
    public function toggleVisibility(CustomerFeedback $customerFeedback)
    {
        $customerFeedback->update([
            'is_public' => !$customerFeedback->is_public
        ]);

        $status = $customerFeedback->is_public ? 'publik' : 'privat';
        
        return redirect()->back()
            ->with('success', "Feedback berhasil diubah menjadi {$status}.");
    }
}