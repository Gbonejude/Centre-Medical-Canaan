<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestAuth
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $guest = Auth::guard('guest')->user();

        if (! $guest) {
            return $this->redirectToLogin($request, 'You must be logged in to access this page.');
        }

        if (! $guest->active) {

            Auth::guard('guest')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->redirectToLogin(
                $request,
                'Your account has been deactivated. Please contact the administrator for assistance.',
                'error'
            );
        }

        return $next($request);
    }

    /**
     * Redirect to login with intended URL and message
     */
    private function redirectToLogin(Request $request, string $message, string $type = 'message')
    {
        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()
            ->route('auth.guest.login.form')
            ->with($type, $message);
    }
}
