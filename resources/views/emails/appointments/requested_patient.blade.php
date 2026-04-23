<x-mail::message>
# Demande de rendez-vous reçue

Bonjour {{ $appointment->patient->firstname }},

Nous avons bien reçu votre demande de rendez-vous pour le service **{{ $appointment->medicalService->name }}**.

**Détails de la demande :**
*   **Date :** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
*   **Heure :** {{ $appointment->appointment_time }}
*   **Statut :** En attente de confirmation

Notre équipe va examiner votre demande et vous recevrez un email de confirmation dès qu'un créneau vous sera attribué.

<x-mail::button :url="route('front.appointments.mine')">
Voir mes rendez-vous
</x-mail::button>

Merci de votre confiance,<br>
L'équipe du {{ config('app.name') }}
</x-mail::message>
