<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role if provided
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,mechanic,customer',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Load relationships based on user role
        $user->load($this->getRelationshipsForUser($user));
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // Prevent non-admin users from editing other users
        if (!Auth::user()->isAdmin() && Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Prevent non-admin users from editing other users
        if (!Auth::user()->isAdmin() && Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        // Only admin can change roles
        if (Auth::user()->isAdmin()) {
            $rules['role'] = 'required|in:admin,mechanic,customer';
        }

        $validated = $request->validate($rules);

        // Only update password if it's provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        $redirectRoute = Auth::user()->isAdmin() ? 'users.index' : 'users.show';
        $redirectParam = Auth::user()->isAdmin() ? [] : [$user->id];

        return redirect()->route($redirectRoute, $redirectParam)
            ->with('success', 'Informasi user berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Prevent deletion of the currently logged-in user
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        // Check if user has related data
        $hasVehicles = $user->vehicles()->count() > 0;
        $hasAppointments = $user->appointments()->count() > 0;
        $hasWorkOrders = $user->workOrders()->count() > 0 || $user->mechanicWorkOrders()->count() > 0;

        if ($hasVehicles || $hasAppointments || $hasWorkOrders) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus user yang memiliki data terkait (kendaraan, appointment, atau work order).');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Display customers only.
     *
     * @return \Illuminate\Http\Response
     */
    public function customers(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount(['vehicles', 'appointments'])
                          ->orderBy('name')
                          ->paginate(10);

        return view('users.customers', compact('customers'));
    }

    /**
     * Display mechanics only.
     *
     * @return \Illuminate\Http\Response
     */
    public function mechanics(Request $request)
    {
        $query = User::where('role', 'mechanic');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $mechanics = $query->withCount('mechanicWorkOrders')
                          ->orderBy('name')
                          ->paginate(10);

        return view('users.mechanics', compact('mechanics'));
    }

    /**
     * Show customer profile with their data.
     *
     * @param  \App\Models\User  $customer
     * @return \Illuminate\Http\Response
     */
    public function customerProfile(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $customer->load([
            'vehicles' => function($query) {
                $query->latest();
            },
            'appointments' => function($query) {
                $query->with('vehicle')->latest();
            },
            'workOrders' => function($query) {
                $query->with(['vehicle', 'mechanic'])->latest();
            }
        ]);

        return view('users.customer-profile', compact('customer'));
    }

    /**
     * Get relationships to load based on user role.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getRelationshipsForUser(User $user)
    {
        $relationships = [];

        if ($user->isCustomer()) {
            $relationships = ['vehicles', 'appointments.vehicle', 'workOrders.vehicle'];
        } elseif ($user->isMechanic()) {
            $relationships = ['mechanicWorkOrders.vehicle', 'mechanicWorkOrders.customer'];
        }

        return $relationships;
    }

    /**
     * Toggle user status (if you want to add active/inactive feature later)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(User $user)
    {
        // This method can be implemented if you add 'status' field to users table
        // $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        
        return redirect()->back()
            ->with('success', 'Status user berhasil diubah.');
    }
}