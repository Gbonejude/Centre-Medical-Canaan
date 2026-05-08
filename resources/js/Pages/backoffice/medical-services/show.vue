<template>
    <Head>
        <title>Détails du Service | {{ service.name }}</title>
        <meta name="description" content="Afficher les détails du service médical" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="navigation-links">
                <Link :href="route('medical-services.index')" class="back-link">
                    <i class="fa fa-arrow-left"></i>
                    <span>Retour aux Services</span>
                </Link>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card profile-card mb-4">
                        <div class="card-body text-center py-5">
                            <div class="service-icon-large">
                                <i class="fa fa-notes-medical"></i>
                            </div>
                            <h3 class="mt-3 mb-1">{{ service.name }}</h3>
                            <div class="badge-container mt-2">
                                <span class="status-badge" :class="service.is_active ? 'active' : 'inactive'">
                                    {{ service.is_active ? 'Service Actif' : 'Service Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card info-card mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa fa-info-circle me-2"></i>
                                <span>Informations sur le Service</span>
                            </div>
                            <div class="card-actions">
                                <Link :href="route('medical-services.edit', service.uuid)" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit me-1"></i>
                                    Modifier
                                </Link>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="detail-section">
                                <div class="detail-item full-width">
                                    <div class="detail-label">Nom du Service</div>
                                    <div class="detail-value fw-600 fs-5">{{ service.name }}</div>
                                </div>
                                <div class="detail-item full-width mt-3">
                                    <div class="detail-label">Description</div>
                                    <div class="detail-value description-text">
                                        {{ service.description || 'Aucune description fournie.' }}
                                    </div>
                                </div>
                            </div>

                            <div class="metadata-section mt-5 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="meta-info">
                                            <i class="fa fa-calendar-plus text-muted me-2"></i>
                                            <span>Créé le : <strong>{{ formatDate(service.created_at) }}</strong></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="meta-info">
                                            <i class="fa fa-calendar-check text-muted me-2"></i>
                                            <span>Dernière mise à jour : <strong>{{ formatDate(service.updated_at) }}</strong></span>
                                        </div>
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
    service: Object,
});

function formatDate(dateString) {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
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

.profile-card {
    .service-icon-large { width: 100px; height: 100px; border-radius: 50%; background-color: rgba($primary-color, 0.1); color: $primary-color; display: flex; align-items: center; justify-content: center; margin: 0 auto; i { font-size: 3rem; } }
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
        .description-text { line-height: 1.6; color: #3f4254; }
    }
}

.meta-info { font-size: 0.9rem; color: #7e8299; }
.fw-600 { font-weight: 600; }
</style>
