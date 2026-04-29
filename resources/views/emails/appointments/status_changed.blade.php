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

@if($appointment->status === 'POSTPONED')
Votre rendez-vous a été reporté à une nouvelle date.

**Nouvelles informations :**
*   **Date :** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
*   **Heure :** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
@else
**Informations sur le rendez-vous :**
*   **Date :** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
*   **Heure :** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
@endif

@if($appointment->doctor && $appointment->status !== 'POSTPONED')
*   **Médecin :** Dr. {{ $appointment->doctor->lastname }} {{ $appointment->doctor->firstname }}
@endif

@if($appointment->receptionist_notes)
**Motif / Note de l'administration :**
> {{ $appointment->receptionist_notes }}
@endif

@if($appointment->status === 'CONFIRMED')
Nous vous attendons à l'heure prévue. Merci de vous présenter à l'accueil 15 minutes à l'avance.
@elseif($appointment->status === 'CANCELLED')
Votre rendez-vous a été annulé. Si vous souhaitez reprendre un rendez-vous, vous pouvez le faire via votre espace personnel.
@elseif($appointment->status === 'POSTPONED')
Veuillez noter ces nouveaux changements. Un médecin vous sera réassigné prochainement.
@endif

<x-mail::button :url="route('front.appointments.mine')">
Accéder à mon espace
</x-mail::button>

Cordialement,<br>
L'équipe du {{ config('app.name') }}
</x-mail::message>
