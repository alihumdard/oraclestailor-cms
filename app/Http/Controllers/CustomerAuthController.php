<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\TailorOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('customer.register', ['genders' => TailorOrder::GENDERS]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'gender' => ['required', Rule::in(array_keys(TailorOrder::GENDERS))],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:160', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Welcome! Your account is ready.');
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return view('customer.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login_identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginIdentifier = $request->input('login_identifier');
        
        // Check if identifier is email or phone
        $fieldType = filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Attempt login
        if (Auth::guard('customer')->attempt([$fieldType => $loginIdentifier, 'password' => $request->input('password')], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.dashboard'));
        }

        // If that failed, try the other one just in case (sometimes inputs like phone might be formatted differently)
        $alternativeField = ($fieldType === 'email') ? 'phone' : 'email';
        if (Auth::guard('customer')->attempt([$alternativeField => $loginIdentifier, 'password' => $request->input('password')], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.dashboard'));
        }

        throw ValidationException::withMessages([
            'login_identifier' => 'Those sign-in details did not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
