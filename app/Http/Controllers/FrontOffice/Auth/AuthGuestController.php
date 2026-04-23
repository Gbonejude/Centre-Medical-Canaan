<?php

declare(strict_types=1);

namespace App\Http\Controllers\FrontOffice\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginGuestRequest;
use App\Http\Requests\Guest\StoreRequest;
use App\Mail\NewGuestMail;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class AuthGuestController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm(Request $request)
    {
        return inertia('frontoffice/auth/register');
    }

    /**
     * Handle guest registration.
     */
    public function register(StoreRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            // Create the User record
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'active' => $validated['active'] ?? true,
            ]);

            // Assign the PATIENT role
            $user->assignRole('PATIENT');

            // Create the Patient profile record
            Patient::create([
                'user_id' => $user->id,
            ]);

            DB::commit();

            Auth::guard('guest')->login($user);

            Mail::to($user->email)->send(new NewGuestMail($user->firstname, $user->lastname));

            return redirect()->intended(route('home.index'))
                ->with('success', 'Votre compte a été créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Une erreur est survenue lors de la création de votre compte : ' . $e->getMessage());
        }
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request)
    {
        return inertia('frontoffice/auth/login');
    }

    /**
     * Handle guest login.
     */
    public function login(LoginGuestRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::guard('guest')->attempt($credentials)) {
            return back()->withErrors(['message' => 'Incorrect email address or password.']);
        }

        $guest = Auth::guard('guest')->user();

        if (! $guest->active) {
            Auth::guard('guest')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'message' => 'Your account has been deactivated. Please contact the administrator for assistance.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('home.index'));
    }

    /**
     * Handle guest logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('guest')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home.index')
            ->with('success', 'You have been logged out successfully.');
    }
}
