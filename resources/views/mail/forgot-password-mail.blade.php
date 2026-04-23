<x-mail::message>
# Password Reset Request

Hello!

We received a request to reset the password for your account at {{ config('app.name') }}.

**Email**: {{ $email }}

Click the button below to reset your password:

<x-mail::button :url="$resetUrl">
    Reset Password
</x-mail::button>

This password reset link will expire in **60 minutes**.

If you did not request a password reset, please ignore this email. Your password will remain unchanged.

For security reasons, please do not share this link with anyone.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>
