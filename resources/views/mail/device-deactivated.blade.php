<x-mail::message>
# Device Access Revoked

Hello {{ $device->user->firstname }},

Your device access has been revoked by an administrator.

**Device Info**
- Device: {{ $device->device_name }}
- OS: {{ $device->device_os }}
- Browser: {{ $device->browser }}

You will no longer be able to access your account from this device. Please contact your administrator if you need to regain access.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
