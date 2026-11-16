<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('tailor.users', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'pending',
        ]);

        return back()->with('success', 'New workroom user added.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove your own account while signed in.');
        }

        $user->delete();

        return back()->with('success', 'User removed.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->status = ($user->status === 'active') ? 'pending' : 'active';
        $user->save();

        return back()->with('success', 'User status updated to ' . $user->status . '.');
    }
}
