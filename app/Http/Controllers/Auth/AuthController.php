<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to login the user
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on user role
            $user = Auth::user();
            
            // Validate user role
            if (!in_array($user->role, ['admin', 'mechanic', 'customer'])) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account does not have a valid role.'],
                ]);
            }

            // Redirect to appropriate dashboard
            return redirect()->intended($this->getDashboardRoute());
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get dashboard route based on user role.
     *
     * @return string
     */
    protected function getDashboardRoute()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        } elseif ($user->isMechanic()) {
            return route('mechanic.dashboard');
        } elseif ($user->isCustomer()) {
            return route('customer.dashboard');
        }
        
        // Default fallback
        return '/';
    }
}