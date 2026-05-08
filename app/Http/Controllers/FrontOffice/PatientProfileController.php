<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PatientProfileController extends Controller
{
    public function index()
    {
        $user = auth()->id() ? auth()->user() : auth()->guard('guest')->user();

        return Inertia::render('frontoffice/profile/index', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->id() ? auth()->user() : auth()->guard('guest')->user();

        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:20|unique:users,phone,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }
}
