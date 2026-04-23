<x-mail::message>
# Hello!

Welcome to {{ config('app.name') }}!

Your account has been successfully created. Here are your login details:<br>
**Email**: {{ $email }}<br>
**Temporary Password**: {{ $password }}<br>

Please login and change your password as soon as possible for security reasons.

Thank you for choosing Canaan Care Services. If you have any questions, please contact our support team.

Thanks,<br>
<br>
Service Support - {{ config('app.name') }}
</x-mail::message>
