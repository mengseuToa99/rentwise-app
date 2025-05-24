<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminLandlordController extends Controller
{
    public function index()
    {
        $landlords = User::whereHas('roles', function($query) {
            $query->where('role_name', 'landlord');
        })->with(['roles', 'properties'])->paginate(10);

        return view('admin.landlords.index', compact('landlords'));
    }

    public function create()
    {
        return view('admin.landlords.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'],
            'password_hash' => Hash::make($validated['password']),
        ]);

        $landlordRole = Role::where('role_name', 'landlord')->first();
        $user->roles()->attach($landlordRole);

        return redirect()->route('admin.landlords.index')
            ->with('success', 'Landlord created successfully.');
    }

    public function edit(User $landlord)
    {
        return view('admin.landlords.edit', compact('landlord'));
    }

    public function update(Request $request, User $landlord)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $landlord->user_id . ',user_id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $landlord->user_id . ',user_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $landlord->update($validated);

        return redirect()->route('admin.landlords.index')
            ->with('success', 'Landlord updated successfully.');
    }

    public function destroy(User $landlord)
    {
        $landlord->delete();

        return redirect()->route('admin.landlords.index')
            ->with('success', 'Landlord deleted successfully.');
    }
} 