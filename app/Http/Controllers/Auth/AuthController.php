<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Models\User;
use App\Notifications\NewDeviceAlertNotification;
use App\Notifications\NewDeviceDetectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

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

        if (! $user->isSystemAdmin()) {
            $staffPermissions = [
                'SUPER ADMIN',
                'ADMIN',
                'OFFICE',
            ];

            $employeeUsers = User::whereHas('permissions', function ($q) use ($staffPermissions) {
                $q->whereIn('name', $staffPermissions);
            })->get();

            $agent = new Agent;

            $deviceName = $agent->device();
            $deviceOs = $agent->platform();
            $osVersion = $agent->version($deviceOs);
            $browser = $agent->browser();
            $browserVersion = $agent->version($browser);
            $ip = $request->ip();
            $deviceType = $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop');
            $userAgent = $request->userAgent();

            if (empty($deviceName) || $deviceName === 'WebKit') {
                $deviceName = $deviceOs.' '.$osVersion.' - '.$browser;
            }

            // Fingerprint stable : sans version navigateur ni User-Agent complet
            // → résiste aux mises à jour automatiques du navigateur
            $fingerprint = hash('sha256', $deviceOs.'|'.$browser.'|'.$deviceType);

            $existingDevice = $user->devices()
                ->where('device_fingerprint', $fingerprint)
                ->first();

            // Fallback : chercher un device autorisé avec même OS + navigateur
            // (migration des anciens fingerprints ou après changement de version navigateur)
            if (! $existingDevice) {
                $existingDevice = $user->devices()
                    ->where('device_os', $deviceOs)
                    ->where('browser', $browser)
                    ->where('is_authorized', true)
                    ->latest()
                    ->first();

                if ($existingDevice) {
                    // Mettre à jour vers le nouveau fingerprint stable
                    $existingDevice->update([
                        'device_fingerprint' => $fingerprint,
                        'ip_address' => $ip,
                        'user_agent' => $userAgent,
                        'device_name' => $deviceName,
                    ]);
                }
            }

            if (! $existingDevice) {
                $device = $user->devices()->create([
                    'device_name' => $deviceName,
                    'device_os' => $deviceOs,
                    'browser' => $browser,
                    'device_type' => $deviceType,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'device_fingerprint' => $fingerprint,
                    'is_authorized' => false,
                ]);

                if ($device) {
                    foreach ($employeeUsers as $employee) {
                        $employee->notify(new NewDeviceAlertNotification($device, $user));
                    }
                }
                $user->notify(new NewDeviceDetectedNotification($device, $user));

                Auth::logout();

                return back()->withErrors([
                    'message' => "THIS IS A HIPAA PROTECTED APP.\nALL INFORMATION IN THIS APP IS CONFIDENTIAL.\n\n\nIt appears you are accessing your account on this device for the first time. Please contact your administrator or call (+1) 703-297-8425 for assistance.",
                ]);
            }

            if (! $existingDevice->is_authorized) {
                Auth::logout();

                return back()->withErrors([
                    'message' => 'This device is no more authorized. Please contact administrator or call (+1) 703-297-8425 for approval.',
                ]);
            }
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
