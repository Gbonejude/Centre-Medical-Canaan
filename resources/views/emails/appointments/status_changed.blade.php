<x-mail::message>
# Mise à jour de votre rendez-vous

Bonjour {{ $appointment->patient->firstname }},

Le statut de votre rendez-vous pour le service **{{ $appointment->medicalService->name }}** a été mis à jour.

**Nouveau statut :** {{ 
    match($appointment->status) {
        'CONFIRMED' => 'Confirmé',
        'CANCELLED' => 'Annulé',
        'COMPLETED' => 'Terminé',
        'POSTPONED' => 'Reporté',
        default => $appointment->status
    }
}}

**Informations sur le rendez-vous :**
*   **Date :** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
*   **Heure :** {{ $appointment->appointment_time }}
@if($appointment->doctor)
*   **Médecin :** Dr. {{ $appointment->doctor->lastname }} {{ $appointment->doctor->firstname }}
@endif

@if($appointment->status === 'CONFIRMED')
Nous vous attendons à l'heure prévue. Merci de vous présenter à l'accueil 15 minutes à l'avance.
@elseif($appointment->status === 'CANCELLED')
Votre rendez-vous a été annulé. Si vous souhaitez reprendre un rendez-vous, vous pouvez le faire via votre espace personnel.
@endif

<x-mail::button :url="route('front.appointments.mine')">
Accéder à mon espace
</x-mail::button>

Cordialement,<br>
L'équipe du {{ config('app.name') }}
</x-mail::message>
