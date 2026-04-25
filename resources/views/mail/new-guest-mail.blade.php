<x-mail::message>
# Bonjour {{ $firstname }} {{ $lastname }} !

Bienvenue sur {{ config('app.name') }} !

Votre compte a été créé avec succès. <br>

Vous pouvez dès à présent vous connecter et accéder à toutes nos fonctionnalités.

Merci d'avoir choisi le Centre Medical Canaan. Si vous avez des questions, n'hésitez pas à contacter notre équipe support.

Cordialement,<br>
<br>
Support Client - {{ config('app.name') }}
</x-mail::message>
