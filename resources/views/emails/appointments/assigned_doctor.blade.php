<x-mail::message>
# Nouveau rendez-vous assigné

Bonjour Dr. {{ $appointment->doctor->lastname }} {{ $appointment->doctor->firstname }},

Un rendez-vous vous a été assigné. Veuillez consulter les détails ci-dessous.

**Informations du patient :**
* **Nom :** {{ $appointment->patient->firstname }} {{ $appointment->patient->lastname }}
* **Téléphone :** {{ $appointment->patient->phone ?? 'Non renseigné' }}

**Détails du rendez-vous :**
* **Référence :** #{{ $appointment->reference }}
* **Service :** {{ $appointment->medicalService->name }}
* **Date :** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
* **Heure :** {{ $appointment->appointment_time }}
* **Motif :** {{ $appointment->reason ?? 'Non renseigné' }}
@if($appointment->receptionist_notes)
* **Notes de la réception :** {{ $appointment->receptionist_notes }}
@endif

<x-mail::button :url="route('appointments.show', $appointment->uuid)">
Voir le rendez-vous
</x-mail::button>

Ceci est une notification automatique du Centre Médical Canaan.
</x-mail::message>
