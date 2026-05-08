<template>
    <Head>
        <title>Profil Médecin | Dr. {{ doctor.user.lastname }}</title>
        <meta name="description" content="Afficher le profil détaillé du médecin" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="navigation-links">
                <Link :href="route('doctors.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour à l'Équipe</span>
                </Link>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card profile-card mb-4">
                        <div class="card-body text-center py-5">
                            <div class="doctor-avatar-large">
                                <img :src="userAvatar || '/assets/img/user.jpg'" alt="Profil" />
                                <div class="status-indicator" :class="doctor.is_available ? 'active' : 'inactive'"></div>
                            </div>
                            <h3 class="mt-4 mb-1">Dr. {{ doctor.user.firstname }} {{ doctor.user.lastname }}</h3>
                            <p class="text-primary fw-600 mb-3">
                                {{ doctor.specialties && doctor.specialties.length > 0 ? doctor.specialties.map(s => s.name).join(', ') : 'Aucune spécialité' }}
                            </p>
                            
                            <div class="badge-container">
                                <span class="status-badge" :class="doctor.is_available ? 'active' : 'inactive'">
                                    {{ doctor.is_available ? 'Disponible' : 'Indisponible' }}
                                </span>
                            </div>

                            <div class="contact-quick-links mt-4 pt-4 border-top">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a :href="`mailto:${doctor.user.email}`" class="btn btn-light btn-sm w-100">
                                            <i class="fa fa-envelope me-1"></i> Email
                                        </a>
                                    </div>
                                    <div class="col-6" v-if="doctor.user.phone">
                                        <a :href="`tel:${doctor.user.phone}`" class="btn btn-light btn-sm w-100">
                                            <i class="fa fa-phone me-1"></i> Appeler
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card info-card mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa fa-id-card me-2"></i>
                                <span>Détails du Profil Professionnel</span>
                            </div>
                            <div class="card-actions">
                                <Link :href="route('doctors.edit', doctor.uuid)" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit me-1"></i> Modifier
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="detail-section">
                                <div class="detail-item">
                                    <div class="detail-label">Service Médical</div>
                                    <div class="detail-value d-flex flex-wrap gap-1">
                                        <span v-if="doctor.medical_service" class="badge bg-light text-primary border">
                                            <i class="fa fa-stethoscope me-1"></i> {{ doctor.medical_service.name }}
                                        </span>
                                        <span v-else class="text-muted">—</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Spécialités</div>
                                    <div class="detail-value d-flex flex-wrap gap-1">
                                        <span v-for="spec in doctor.specialties" :key="spec.id" class="badge bg-light text-success border">
                                            <i class="fa fa-award me-1"></i> {{ spec.name }}
                                        </span>
                                        <span v-if="!doctor.specialties || doctor.specialties.length === 0" class="text-muted">—</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Adresse Email</div>
                                    <div class="detail-value">{{ doctor.user.email }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Téléphone</div>
                                    <div class="detail-value">{{ doctor.user.phone || 'Non renseigné' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Genre</div>
                                    <div class="detail-value">{{ doctor.user.gender === 'male' ? 'Masculin' : (doctor.user.gender === 'female' ? 'Féminin' : 'Non précisé') }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Date de naissance</div>
                                    <div class="detail-value">{{ doctor.user.birthday ? formatDate(doctor.user.birthday) : 'Non renseignée' }}</div>
                                </div>
                                <div class="detail-item full-width mt-2">
                                    <div class="detail-label">Biographie</div>
                                    <div class="detail-value bio-text">
                                        {{ doctor.bio || 'Aucune biographie fournie.' }}
                                    </div>
                                </div>
                            </div>

                            <div class="meta-section mt-4 pt-4 border-top">
                                <div class="row text-muted small">
                                    <div class="col-md-6">
                                        Membre depuis le : {{ formatDate(doctor.created_at) }}
                                    </div>
                                </div>
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
    doctor: Object,
    userAvatar: String,
});

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
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

.doctor-avatar-large {
    width: 120px; height: 120px; border-radius: 50%; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin: 0 auto; position: relative;
    img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .status-indicator { position: absolute; bottom: 5px; right: 5px; width: 22px; height: 22px; border-radius: 50%; border: 4px solid white;
        &.active { background-color: $success-color; }
        &.inactive { background-color: $danger-color; }
    }
}

.status-badge {
    padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
    &.active { background-color: rgba($success-color, 0.1); color: $success-color; }
    &.inactive { background-color: rgba($danger-color, 0.1); color: $danger-color; }
}

.info-card {
    .card-header { background: white; padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; display: flex; justify-content: space-between; align-items: center; .card-title { font-weight: 600; font-size: 1.1rem; } }
    .card-body { padding: 1.5rem; }
}

.detail-section {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;
    .detail-item {
        &.full-width { grid-column: span 2; }
        .detail-label { font-size: 0.85rem; color: #7e8299; margin-bottom: 0.4rem; }
        .detail-value { font-weight: 500; color: #181c32; }
        .bio-text { line-height: 1.6; color: #3f4254; }
    }
}

.fw-600 { font-weight: 600; }
</style>
