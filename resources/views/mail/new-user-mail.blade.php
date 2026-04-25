<x-mail::message>
# Bonjour !

Bienvenue sur {{ config('app.name') }} !

Votre compte a été créé avec succès. Voici vos informations de connexion :<br><br>
**Email** : {{ $email }}<br>
**Mot de passe temporaire** : {{ $password }}<br>

<x-mail::button :url="route('auth.loginForm')">
    Se connecter
</x-mail::button>
Veuillez vous connecter et modifier votre mot de passe dès que possible pour des raisons de sécurité.

Merci d'avoir choisi le Centre Medical Canaan. Si vous avez des questions, n'hésitez pas à contacter notre équipe support.

Cordialement,<br>
<br>
Support Client - {{ config('app.name') }}
</x-mail::message>
