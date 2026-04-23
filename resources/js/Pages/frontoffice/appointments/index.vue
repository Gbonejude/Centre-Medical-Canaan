<template>
    <DefaultLayout>
        <div class="appointments-page py-5">
            <div class="container">
                <div class="header-section mb-5 text-center">
                    <h2 class="fw-bold">Mes Rendez-vous</h2>
                    <p class="text-muted">Consultez l'historique et le statut de vos demandes.</p>
                    <Link :href="route('appointments.create')" class="btn btn-primary rounded-pill px-4 mt-3">
                        <i class="fa fa-plus me-2"></i> Nouvelle Réservation
                    </Link>
                </div>

                <div class="row">
                    <div v-for="app in my_appointments" :key="app.id" class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="service-badge bg-light p-2 rounded-3 text-primary">
                                        <i class="fa fa-stethoscope"></i> {{ app.service.name }}
                                    </div>
                                    <span :class="getStatusBadge(app.status)">{{ app.status }}</span>
                                </div>
                                <h5 class="card-title fw-bold mb-1">
                                    {{ app.doctor ? 'Dr. ' + app.doctor.name : 'En attente de médecin' }}
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="fa fa-calendar me-2"></i> {{ app.appointment_date }}
                                    <i class="fa fa-clock-o ms-3 me-2"></i> {{ app.appointment_time }}
                                </p>
                                <div v-if="app.receptionist_notes" class="alert alert-info py-2 small mb-0">
                                    <strong>Note de l'accueil :</strong> {{ app.receptionist_notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="my_appointments.length === 0" class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fa fa-calendar-times-o fa-4x text-muted opacity-25"></i>
                            <h4 class="mt-3 text-muted">Aucun rendez-vous trouvé</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import DefaultLayout from "../../components/layouts/DefaultLayout.vue";

const props = defineProps({
    my_appointments: Array,
});

const getStatusBadge = (status) => {
    let classes = 'badge rounded-pill ';
    switch (status) {
        case 'PENDING': return classes + 'bg-warning text-dark';
        case 'CONFIRMED': return classes + 'bg-success';
        case 'COMPLETED': return classes + 'bg-info';
        case 'CANCELLED': return classes + 'bg-danger';
        default: return classes + 'bg-secondary';
    }
};
</script>

<style scoped>
.appointments-page { background-color: #f7f9fc; min-height: 90vh; }
.card { transition: transform 0.2s; }
.card:hover { transform: translateY(-5px); }
</style>
