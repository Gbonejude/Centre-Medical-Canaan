<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLoginForm()
    {

        if (auth()->user()) {
            return redirect()->route('dashboard.index');
        }

        return inertia('auth/Login');
    }

    public function showForgotForm()
    {

        return inertia('auth/forgot');
    }

    public function showResetForm()
    {

        return inertia('auth/reset');
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['message' => 'Incorrect email address or password.']);
        }

        $user = auth()->user();

        if (! $user->active) {
            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return back()->withErrors([
                'message' => 'Your account has been deactivated. Please contact the administrator for assistance.',
            ]);
        }

        $request->session()->regenerate();

        $request->session()->save();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();

        return Inertia::location(route('dashboard.index'));
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            session()->flash('success', 'Logout successful');

            return Inertia::location(route('auth.loginForm'));

        } catch (\Exception $e) {
            return redirect()->route('auth.loginForm')->with('error', 'An error occurred during logout. Please try again.');
        }
    }
}
