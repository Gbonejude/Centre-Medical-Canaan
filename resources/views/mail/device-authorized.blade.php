<x-mail::message>
# Device Authorized

Hello {{ $device->user->firstname }},

Your device has been successfully authorized.

**Device Info**
- Device: {{ $device->device_name }}
- OS: {{ $device->device_os }}
- Browser: {{ $device->browser }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
