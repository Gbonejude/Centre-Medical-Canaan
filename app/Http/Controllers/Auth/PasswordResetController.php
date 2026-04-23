<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\ForgotPasswordMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotForm()
    {
        return inertia('auth/forgot');
    }

    /**
     * Handle forgot password request.
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return back()->withErrors([
                    'email' => 'No account found with this email address.',
                ]);
            }

            if (! $user->active) {
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the administrator.',
                ]);
            }

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            $token = Str::random(64);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            $resetUrl = route('auth.password.reset.form', ['token' => $token, 'email' => $request->email]);

            Mail::to($request->email)->send(new ForgotPasswordMail($resetUrl, $request->email));

            return back()->with('success', 'Password reset link has been sent to your email address.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'An error occurred while processing your request. Please try again.',
            ]);
        }
    }

    /**
     * Show the reset password form.
     */
    public function showResetForm(Request $request, string $token)
    {
        return inertia('auth/reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (! $passwordReset) {
                return back()->withErrors([
                    'email' => 'Invalid or expired password reset token.',
                ]);
            }

            if (now()->diffInMinutes($passwordReset->created_at) > 60) {
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return back()->withErrors([
                    'email' => 'This password reset link has expired. Please request a new one.',
                ]);
            }

            if (! Hash::check($request->token, $passwordReset->token)) {
                return back()->withErrors([
                    'email' => 'Invalid password reset token.',
                ]);
            }

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return back()->withErrors([
                    'email' => 'No account found with this email address.',
                ]);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            Mail::to($request->email)->send(new ResetPasswordMail($request->email));

            return redirect()->route('auth.loginForm')
                ->with('success', 'Your password has been reset successfully. You can now login with your new password.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'An error occurred while resetting your password. Please try again.',
            ]);
        }
    }
}
