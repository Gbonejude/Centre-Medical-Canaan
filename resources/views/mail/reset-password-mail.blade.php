<x-mail::message>
# Password Successfully Reset

Hello!

This email confirms that your password for {{ config('app.name') }} has been successfully reset.

**Email**: {{ $email }}

You can now log in to your account using your new password.

<x-mail::button :url="route('auth.loginForm')">
    Login to Your Account
</x-mail::button>

If you did not make this change or believe an unauthorized person has accessed your account, please contact our support team immediately.

For your security, we recommend:
- Using a strong, unique password
- Changing your password regularly
- Not sharing your password with anyone

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>
