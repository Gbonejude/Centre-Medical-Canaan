<x-mail::message>
# New Device Login Attempt

Hello {{ $user->firstname }},

A new device has attempted to access your account.

**Device Information**
- Device: {{ $device->device_name }}
- OS: {{ $device->device_os }}
- Browser: {{ $device->browser }}

If you did not make this attempt, please change your password immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
