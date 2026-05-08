<x-mail::message>
# Mot de passe réinitialisé avec succès

Bonjour !

Cet email confirme que votre mot de passe pour {{ config('app.name') }} a été réinitialisé avec succès.

**Email** : {{ $email }}

Vous pouvez maintenant vous connecter à votre compte en utilisant votre nouveau mot de passe.

<x-mail::button :url="route('auth.loginForm')">
    Se connecter à votre compte
</x-mail::button>

Si vous n'avez pas effectué ce changement ou si vous pensez qu'une personne non autorisée a accédé à votre compte, veuillez contacter notre équipe support immédiatement.

Pour votre sécurité, nous vous recommandons :
- D'utiliser un mot de passe fort et unique
- De modifier votre mot de passe régulièrement
- De ne partager votre mot de passe avec personne

Cordialement,<br>
<br>
Support Client - {{ config('app.name') }}
</x-mail::message>
