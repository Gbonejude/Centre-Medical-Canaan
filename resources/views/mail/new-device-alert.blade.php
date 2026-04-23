<x-mail::message>
# New Device Detected for {{ $user->firstname }} {{ $user->lastname }}

A login attempt was made from a new device.

**User:**
{{ $user->firstname }} {{ $user->lastname }}

**Device Info:**
- Device: {{ $device->device_name }}
- OS: {{ $device->device_os }}
- Browser: {{ $device->browser }}
- IP: {{ $device->ip_address }}

If you don't recognize this attempt, please change your password immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
