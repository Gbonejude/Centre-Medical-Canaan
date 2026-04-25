<template>
    <div class="appointments-history py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Mes Rendez-vous</h2>
                <Link :href="route('front.appointments.create')" class="btn btn-primary rounded-pill px-4">
                    <i class="fa fa-plus me-2"></i>Nouveau Rendez-vous
                </Link>
            </div>

            <div v-if="appointments.length > 0" class="tabs-container mb-5">
                <div class="premium-tabs">
                    <button class="tab-item" :class="{ active: selectedStatus === 'all' }" @click="selectedStatus = 'all'">
                        <i class="fa fa-list me-2"></i>
                        Tous
                        <span class="badge-count ms-2">{{ appointments.length }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: selectedStatus === 'PENDING' }" @click="selectedStatus = 'PENDING'">
                        <i class="fa fa-inbox me-2"></i>
                        En attente
                        <span class="badge-count ms-2" v-if="counts.pending > 0">{{ counts.pending }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: selectedStatus === 'CONFIRMED' }" @click="selectedStatus = 'CONFIRMED'">
                        <i class="fa fa-calendar-check me-2"></i>
                        Confirmés
                        <span class="badge-count ms-2" v-if="counts.confirmed > 0">{{ counts.confirmed }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: selectedStatus === 'today' }" @click="selectedStatus = 'today'">
                        <i class="fa fa-clock me-2"></i>
                        Aujourd'hui
                        <span class="badge-today ms-2" v-if="counts.today > 0">{{ counts.today }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: selectedStatus === 'CANCELLED' }" @click="selectedStatus = 'CANCELLED'">
                        <i class="fa fa-times-circle me-2"></i>
                        Annulés
                        <span class="badge-count ms-2" v-if="counts.cancelled > 0">{{ counts.cancelled }}</span>
                    </button>
                </div>
            </div>

            <div v-if="filteredAppointments.length === 0 && appointments.length > 0" class="text-center py-5">
                <i class="fa fa-search fa-3x text-muted opacity-25 mb-3"></i>
                <h5 class="text-muted">Aucun rendez-vous ici.</h5>
                <button class="btn btn-link" @click="selectedStatus = 'all'">Voir tout</button>
            </div>

            <div v-else class="row g-4">
                <div v-for="appt in filteredAppointments" :key="appt.id" class="col-md-6 col-lg-4">
                    <div class="appointment-card bg-white rounded-4 shadow-sm border-0 h-100 overflow-hidden transition-hover">
                        <div :class="['status-bar', getStatusClass(appt.status)]"></div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span :class="['badge rounded-pill px-3 py-2', getBadgeClass(appt.status)]">
                                    {{ translateStatus(appt.status) }}
                                </span>
                                <div class="dropdown" v-if="['PENDING', 'CANCELLED'].includes(appt.status)">
                                    <button class="btn btn-link text-muted p-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v fa-lg px-2"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                        <li v-if="appt.status === 'PENDING'">
                                            <Link :href="route('front.appointments.edit', appt.uuid)" class="dropdown-item py-2">
                                                <i class="fa fa-edit me-2 text-primary"></i> Modifier
                                            </Link>
                                        </li>
                                        <li v-if="appt.status === 'PENDING'">
                                            <button class="dropdown-item py-2 text-danger" @click.prevent="openCancelModal(appt)">
                                                <i class="fa fa-times me-2"></i> Annuler
                                            </button>
                                        </li>
                                        <li v-if="appt.status === 'CANCELLED'">
                                            <button class="dropdown-item py-2 text-danger" @click.prevent="deleteAppointment(appt)">
                                                <i class="fa fa-trash me-2"></i> Supprimer définitivement
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h5 class="fw-bold mb-1">{{ appt.medical_service?.name }}</h5>
                            <p class="text-muted mb-3 small" v-if="appt.doctor">
                                <i class="fa fa-user-md me-1 text-primary"></i> Dr. {{ appt.doctor.lastname }}
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

        <!-- Cancel Modal -->
        <div v-if="showCancelModal" class="modal-backdrop fade show"></div>
        <div v-if="showCancelModal" class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">Confirmer l'annulation</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Êtes-vous sûr de vouloir annuler ce rendez-vous ? Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" @click="closeModal">Non, fermer</button>
                        <button 
                            v-if="selectedAppt"
                            type="button"
                            class="btn btn-danger rounded-pill px-4"
                            @click="confirmCancel"
                        >
                            Oui, annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const props = defineProps({
    appointments: Array
})

const selectedStatus = ref('all')
const todayStr = new Date().toISOString().split('T')[0];

const counts = computed(() => {
    return {
        pending: props.appointments.filter(a => a.status === 'PENDING').length,
        confirmed: props.appointments.filter(a => a.status === 'CONFIRMED' || a.status === 'POSTPONED').length,
        today: props.appointments.filter(a => a.appointment_date === todayStr).length,
        cancelled: props.appointments.filter(a => a.status === 'CANCELLED').length,
    }
})

const filteredAppointments = computed(() => {
    if (selectedStatus.value === 'all') return props.appointments
    if (selectedStatus.value === 'today') return props.appointments.filter(a => a.appointment_date === todayStr)
    return props.appointments.filter(a => a.status === selectedStatus.value)
})

const showCancelModal = ref(false)
const selectedAppt = ref(null)
const toast = useToast()
const page = usePage()

const deleteAppointment = (appt) => {
    if (confirm('Voulez-vous vraiment supprimer définitivement ce rendez-vous de votre historique ?')) {
        router.delete(route('front.appointments.destroy', appt.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Rendez-vous supprimé définitivement.');
            },
            onError: () => {
                toast.error("Erreur lors de la suppression.");
            }
        });
    }
}

const openCancelModal = (appt) => {
    selectedAppt.value = appt
    showCancelModal.value = true
    document.body.classList.add('modal-open')
}

const closeModal = () => {
    showCancelModal.value = false
    setTimeout(() => {
        selectedAppt.value = null
        document.body.classList.remove('modal-open')
    }, 150)
}

const confirmCancel = () => {
    if (!selectedAppt.value) return;
    
    router.patch(route('front.appointments.cancel', selectedAppt.value.uuid), {}, {
        preserveScroll: true,
        onSuccess: () => {
            if (page.props.flash?.success) {
                toast.success(page.props.flash.success);
            }
            if (page.props.flash?.error) {
                toast.error(page.props.flash.error);
            }
            closeModal();
        },
        onError: () => {
            toast.error("Une erreur s'est produite lors de l'annulation.");
            closeModal();
        }
    });
}

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

.tabs-container {
    .premium-tabs {
        display: flex; gap: 0.5rem; background: #eaecf0; padding: 0.4rem; border-radius: 12px; display: inline-flex; flex-wrap: wrap;
        .tab-item {
            padding: 0.6rem 1.25rem; border: none; background: transparent; border-radius: 10px; font-weight: 600; color: #667085; transition: all 0.2s;
            display: flex; align-items: center;
            &.active { background: white; color: #4361ee; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
            .badge-count { background: #e5e7eb; color: #374151; font-size: 0.7rem; border-radius: 50px; padding: 0.2rem 0.5rem; }
            .badge-today { background: rgba(#0abb87, 0.15); color: #0abb87; font-size: 0.7rem; border-radius: 50px; padding: 0.2rem 0.5rem; }
        }
    }
}
</style>
