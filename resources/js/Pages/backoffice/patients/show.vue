<template>
    <Head>
        <title>Dossier Patient | {{ patient.user.lastname }}</title>
        <meta name="description" content="Informations détaillées du patient" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="navigation-links">
                <Link :href="route('patients.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour aux Patients</span>
                </Link>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card profile-card mb-4">
                        <div class="card-body text-center py-5">
                            <div class="patient-avatar-large">
                                <img :src="patient.user.avatar_url || '/assets/img/user.jpg'" alt="Profil" />
                            </div>
                            <h3 class="mt-4 mb-1">{{ patient.user.firstname }} {{ patient.user.lastname }}</h3>
                            <p class="text-muted mb-3">ID Patient: #PAT-{{ patient.id }}</p>
                            
                            <div class="badge-container mb-4">
                                <span class="gender-badge" :class="patient.gender?.toLowerCase()">
                                    {{ translateGender(patient.gender) }}
                                </span>
                            </div>

                            <div class="patient-quick-stats pt-4 border-top">
                                <div class="row g-0">
                                    <div class="col-6 border-end">
                                        <div class="stat-label">Âge</div>
                                        <div class="stat-value">{{ calculateAge(patient.date_of_birth) }} ans</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-label">RDVs</div>
                                        <div class="stat-value">{{ patient.appointments?.length || 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card emergency-card mb-4 border-danger-subtle bg-danger-subtle">
                        <div class="card-body">
                            <h6 class="card-title text-danger d-flex align-items-center">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                Contact d'Urgence
                            </h6>
                            <div class="emergency-content mt-2">
                                {{ patient.emergency_contact || 'Non renseigné' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card info-card mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa fa-id-card me-2"></i>
                                <span>Informations Générales</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="detail-section">
                                <div class="detail-item">
                                    <div class="detail-label">Date de Naissance</div>
                                    <div class="detail-value">{{ formatDate(patient.date_of_birth) }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Téléphone</div>
                                    <div class="detail-value">{{ patient.user.phone || '—' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value">{{ patient.user.email }}</div>
                                </div>
                                <div class="detail-item full-width mt-2">
                                    <div class="detail-label">Adresse Résidentielle</div>
                                    <div class="detail-value">{{ patient.address || 'Non renseignée' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card info-card mb-4">
                        <div class="card-header bg-light">
                            <div class="card-title">
                                <i class="fa fa-notes-medical me-2"></i>
                                <span>Antécédents Médicaux</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="medical-history-text">
                                {{ patient.medical_history || 'Aucun antécédent majeur enregistré.' }}
                            </div>
                        </div>
                    </div>

                    <div class="card info-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa fa-calendar-alt me-2"></i>
                                <span>Historique des Rendez-vous</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" v-if="patient.appointments?.length > 0">
                                <table class="table mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Médecin</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="app in patient.appointments" :key="app.id">
                                            <td>{{ formatDateTime(app.appointment_date) }}</td>
                                            <td>Dr. {{ app.doctor?.lastname }}</td>
                                            <td>
                                                <span class="badge" :class="getAppointmentBadgeClass(app.status)">
                                                    {{ app.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center py-4 text-muted">
                                Aucun rendez-vous trouvé pour ce patient.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    patient: Object,
});

function formatDate(date) {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

function formatDateTime(date) {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function calculateAge(dob) {
    if (!dob) return '—';
    const diff = Date.now() - new Date(dob).getTime();
    return Math.abs(new Date(diff).getUTCFullYear() - 1970);
}

function translateGender(gender) {
    if (!gender) return '—';
    const g = gender.toLowerCase();
    if (g === 'male' || g === 'masculin') return 'Masculin';
    if (g === 'female' || g === 'féminin') return 'Féminin';
    return gender;
}

function getAppointmentBadgeClass(status) {
    const s = status?.toLowerCase();
    if (s === 'completed' || s === 'terminé') return 'bg-success';
    if (s === 'pending' || s === 'en attente') return 'bg-warning';
    if (s === 'cancelled' || s === 'annulé') return 'bg-danger';
    return 'bg-secondary';
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$secondary-color: #3f4254;
$success-color: #0abb87;
$danger-color: #f64e60;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 0.475rem;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.navigation-links { margin-bottom: 1.5rem; .back-link { display: inline-flex; align-items: center; text-decoration: none; color: $secondary-color; font-weight: 500; transition: all 0.3s ease; i { margin-right: 0.5rem; } &:hover { color: $primary-color; transform: translateX(-3px); } } }

.card { border: none; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); }

.patient-avatar-large {
    width: 120px; height: 120px; border-radius: 50%; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin: 0 auto;
    img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
}

.patient-quick-stats {
    .stat-label { font-size: 0.75rem; color: #7e8299; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-weight: 700; color: #181c32; font-size: 1.1rem; }
}

.gender-badge {
    padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
    &.masculin, &.male { background-color: rgba($primary-color, 0.1); color: $primary-color; }
    &.féminin, &.female { background-color: rgba(#e83e8c, 0.1); color: #e83e8c; }
}

.blood-badge { padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; background-color: rgba($danger-color, 0.1); color: $danger-color; }

.info-card {
    .card-header { background: white; padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; .card-title { font-weight: 600; font-size: 1.1rem; margin: 0; } }
    .card-body { padding: 1.5rem; }
}

.detail-section {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;
    .detail-item {
        &.full-width { grid-column: span 2; }
        .detail-label { font-size: 0.85rem; color: #7e8299; margin-bottom: 0.4rem; }
        .detail-value { font-weight: 500; color: #181c32; }
    }
}

.medical-history-text { line-height: 1.7; color: #3f4254; white-space: pre-line; }

.emergency-card { .emergency-content { font-weight: 600; color: #181c32; line-height: 1.5; } }
</style>
