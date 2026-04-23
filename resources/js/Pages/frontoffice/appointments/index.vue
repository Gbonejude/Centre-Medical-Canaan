<template>
    <div class="appointments-history py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Mes Rendez-vous</h2>
                <Link :href="route('front.appointments.create')" class="btn btn-primary rounded-pill px-4">
                    <i class="fa fa-plus me-2"></i>Nouveau Rendez-vous
                </Link>
            </div>

            <div v-if="appointments.length === 0" class="empty-state text-center py-5 bg-white rounded-4 shadow-sm">
                <div class="mb-4">
                    <i class="fa fa-calendar-times fa-4x text-muted opacity-25"></i>
                </div>
                <h4 class="text-muted">Vous n'avez pas encore de rendez-vous.</h4>
                <p class="text-muted mb-4">Prenez votre premier rendez-vous avec l'un de nos spécialistes dès maintenant.</p>
                <Link :href="route('front.appointments.create')" class="btn btn-outline-primary rounded-pill px-4">
                    Commencer ici
                </Link>
            </div>

            <div v-else class="row g-4">
                <div v-for="appt in appointments" :key="appt.id" class="col-md-6 col-lg-4">
                    <div class="appointment-card bg-white rounded-4 shadow-sm border-0 h-100 overflow-hidden transition-hover">
                        <div :class="['status-bar', getStatusClass(appt.status)]"></div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span :class="['badge rounded-pill px-3 py-2', getBadgeClass(appt.status)]">
                                    {{ translateStatus(appt.status) }}
                                </span>
                                <span class="text-muted small">
                                    Ref: #{{ appt.id }}
                                </span>
                            </div>

                            <h5 class="fw-bold mb-1">{{ appt.medical_service?.name }}</h5>
                            <p class="text-muted mb-3 small" v-if="appt.doctor">
                                <i class="fa fa-user-md me-1 text-primary"></i> Dr. {{ appt.doctor.user.lastname }}
                            </p>

                            <hr class="my-3 opacity-10">

                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-calendar-alt me-2 text-primary"></i>
                                <span>{{ formatDate(appt.appointment_date) }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-clock me-2 text-primary"></i>
                                <span>{{ formatTime(appt.appointment_time) }}</span>
                            </div>

                            <div v-if="appt.reason" class="mt-3">
                                <p class="text-muted small mb-0 line-clamp-2">
                                    <strong>Motif :</strong> {{ appt.reason }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    appointments: Array
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    return time.substring(0, 5)
}

const translateStatus = (status) => {
    const statuses = {
        'PENDING': 'En attente',
        'CONFIRMED': 'Confirmé',
        'COMPLETED': 'Terminé',
        'CANCELLED': 'Annulé',
        'POSTPONED': 'Reporté'
    }
    return statuses[status] || status
}

const getBadgeClass = (status) => {
    const classes = {
        'PENDING': 'bg-warning-soft text-warning',
        'CONFIRMED': 'bg-primary-soft text-primary',
        'COMPLETED': 'bg-success-soft text-success',
        'CANCELLED': 'bg-danger-soft text-danger',
        'POSTPONED': 'bg-info-soft text-info'
    }
    return classes[status] || 'bg-secondary text-white'
}

const getStatusClass = (status) => {
    const classes = {
        'PENDING': 'bg-warning',
        'CONFIRMED': 'bg-primary',
        'COMPLETED': 'bg-success',
        'CANCELLED': 'bg-danger',
        'POSTPONED': 'bg-info'
    }
    return classes[status] || 'bg-secondary'
}
</script>

<style scoped>
.appointments-history { background-color: #f8f9fa; min-height: 80vh; }
.appointment-card { transition: transform 0.3s ease, box-shadow 0.3s ease; border-left: 1px solid rgba(0,0,0,0.05); }
.appointment-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
.status-bar { height: 4px; width: 100%; }
.bg-warning-soft { background-color: #fff3cd; }
.bg-primary-soft { background-color: #cfe2ff; }
.bg-success-soft { background-color: #d1e7dd; }
.bg-danger-soft { background-color: #f8d7da; }
.bg-info-soft { background-color: #cff4fc; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
