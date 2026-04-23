<x-mail::message>
# Nouvelle demande de rendez-vous

Une nouvelle demande de rendez-vous vient d'être soumise sur la plateforme.

**Détails du patient :**
*   **Nom :** {{ $appointment->patient->firstname }} {{ $appointment->patient->lastname }}
*   **Téléphone :** {{ $appointment->patient->phone }}

**Détails du rendez-vous :**
*   **Service :** {{ $appointment->medicalService->name }}
*   **Date :** {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
*   **Heure :** {{ $appointment->appointment_time }}
*   **Motif :** {{ $appointment->reason ?? 'Non renseigné' }}

Veuillez vous connecter au tableau de bord pour confirmer cette demande ou l'assigner à un médecin.

<x-mail::button :url="route('appointments.index')">
Gérer les rendez-vous
</x-mail::button>

Ceci est une notification automatique.
</x-mail::message>
