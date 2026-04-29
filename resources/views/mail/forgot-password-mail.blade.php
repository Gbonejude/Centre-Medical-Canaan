<x-mail::message>
# Demande de réinitialisation de mot de passe

Bonjour !

Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte sur {{ config('app.name') }}.

**Email** : {{ $email }}

Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :

<x-mail::button :url="$resetUrl">
    Réinitialiser le mot de passe
</x-mail::button>

Ce lien de réinitialisation expirera dans **60 minutes**.

Si vous n'avez pas demandé la réinitialisation de votre mot de passe, veuillez ignorer cet email. Votre mot de passe restera inchangé.

Pour des raisons de sécurité, veuillez ne partager ce lien avec personne.

Cordialement,<br>
<br>
Support Client - {{ config('app.name') }}
</x-mail::message>
