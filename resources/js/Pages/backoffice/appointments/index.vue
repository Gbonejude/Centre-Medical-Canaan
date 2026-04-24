<template>
    <Head>
        <title>Rendez-vous | CMC</title>
        <meta name="description" content="Gérer les rendez-vous médicaux" />
    </Head>

    <div class="content-wrapper">
        <div class="container">

            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-calendar-check me-2"></i>
                            Gestion des Rendez-vous
                        </h4>
                        <p class="text-muted">Gérez les demandes et affectez des médecins</p>
                    </div>
                    <div class="header-actions">
                        <div class="filters-row">
                            <!-- Filtre Date avec DatePickerComponent -->
                            <div class="filter-item date-filter">
                                <i class="fa fa-calendar filter-icon"></i>
                                <DatePickerComponent
                                    v-model="dateFilter"
                                    maxDate="none"
                                    placeholder="Filtrer par date..."
                                />
                                <button v-if="dateFilter" @click="dateFilter = ''" class="clear-btn" title="Effacer la date">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>

                            <!-- Filtre Statut -->
                            <div class="filter-item">
                                <select v-model="statusFilter" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="PENDING">En attente</option>
                                    <option value="CONFIRMED">Confirmé</option>
                                    <option value="COMPLETED">Terminé</option>
                                    <option value="CANCELLED">Annulé</option>
                                </select>
                            </div>

                            <!-- Recherche -->
                            <div class="search-container">
                                <input type="text" v-model="searchQuery" placeholder="Rechercher un patient..." class="form-control" />
                                <i class="fa fa-search search-icon"></i>
                            </div>

                            <!-- Bouton réinitialiser -->
                            <button v-if="hasActiveFilters" @click="resetFilters" class="btn-reset">
                                <i class="fa fa-undo me-1"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Systèmes d'onglets (Tabs) -->
            <div class="tabs-container mb-4">
                <div class="premium-tabs">
                    <button class="tab-item" :class="{ active: currentTab === 'all' }" @click="currentTab = 'all'">
                        <i class="fa fa-list me-2"></i>
                        Tous
                        <span class="badge-count ms-2">{{ allAppointments.length }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: currentTab === 'requests' }" @click="currentTab = 'requests'">
                        <i class="fa fa-inbox me-2"></i>
                        Nouvelles Demandes
                        <span class="badge ms-2" v-if="stats.requests > 0">{{ stats.requests }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: currentTab === 'scheduled' }" @click="currentTab = 'scheduled'">
                        <i class="fa fa-calendar-alt me-2"></i>
                        Planifiés
                        <span class="badge badge-outline ms-2">{{ stats.scheduled }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: currentTab === 'today' }" @click="setToday">
                        <i class="fa fa-sun me-2"></i>
                        Aujourd'hui
                        <span class="badge badge-today ms-2">{{ todayCount }}</span>
                    </button>
                    <button class="tab-item" :class="{ active: currentTab === 'history' }" @click="currentTab = 'history'">
                        <i class="fa fa-history me-2"></i>
                        Historique
                    </button>
                </div>
            </div>

            <!-- Résumé du filtre actif -->
            <div v-if="hasActiveFilters" class="filter-summary mb-3">
                <i class="fa fa-filter me-2"></i>
                <span>{{ filteredAppointments.length }} résultat(s) trouvé(s)</span>
                <span v-if="dateFilter"> · Date : <strong>{{ dateFilter }}</strong></span>
                <span v-if="statusFilter"> · Statut : <strong>{{ translateStatus(statusFilter) }}</strong></span>
                <span v-if="searchQuery"> · Recherche : <strong>"{{ searchQuery }}"</strong></span>
            </div>

            <div class="card data-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Date & Heure</th>
                                    <th v-if="currentTab !== 'requests'">Médecin</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="paginatedAppointments.length > 0">
                                <tr v-for="app in paginatedAppointments" :key="app.id" class="user-row">
                                    <td>
                                        <div class="user-info">
                                            <div class="user-name">{{ app.patient?.lastname }} {{ app.patient?.firstname }}</div>
                                            <div class="text-muted small">{{ app.patient?.phone || 'Pas de tél' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="service-badge">
                                            {{ app.medical_service?.name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-time">
                                            <div class="date-val">{{ formatDate(app.appointment_date) }}</div>
                                            <div class="time-val text-muted small">
                                                <i class="fa fa-clock me-1"></i> {{ formatTime(app.appointment_time) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td v-if="currentTab !== 'requests'">
                                        <div v-if="app.doctor" class="doctor-assigned">
                                            <i class="fa fa-user-md text-primary me-2"></i>
                                            Dr. {{ app.doctor?.lastname }}
                                        </div>
                                        <span v-else class="text-warning small">
                                            <i class="fa fa-exclamation-circle me-1"></i> Non affecté
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge" :class="app.status.toLowerCase()">
                                            {{ translateStatus(app.status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons justify-content-center">
                                            <button v-if="!app.doctor_id" @click="openAssignModal(app)"
                                                class="btn btn-sm btn-primary-gradient" title="Affecter un médecin">
                                                <i class="fa fa-user-plus me-1"></i> Affecter
                                            </button>
                                            <Link :href="route('appointments.show', app.id)"
                                                class="btn btn-sm btn-outline-info ms-2" title="Voir détails">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td :colspan="currentTab === 'requests' ? 5 : 6" class="text-center py-5">
                                        <div class="empty-state text-muted">
                                            <i class="fa fa-calendar-times fa-3x mb-3"></i>
                                            <p>Aucun rendez-vous trouvé pour ces critères.</p>
                                            <button v-if="hasActiveFilters" @click="resetFilters" class="btn btn-sm btn-outline-secondary mt-2">
                                                Effacer les filtres
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container" v-if="totalPages > 1">
                        <div class="pagination-info">
                            Affichage de {{ paginationFrom }} à {{ paginationTo }} sur {{ filteredAppointments.length }} rendez-vous
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">&laquo;</button>
                                </li>
                                <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                                    <button class="page-link" @click="currentPage = page">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="currentPage++" :disabled="currentPage === totalPages">&raquo;</button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'affectation -->
    <div v-if="showModal" class="modal-backdrop" @click="closeModal">
        <div class="modal-content" @click.stop>
            <div class="modal-header">
                <h5 class="modal-title">Affecter un Médecin</h5>
                <button @click="closeModal" class="btn-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="appointment-summary mb-4">
                    <p><strong>Patient:</strong> {{ selectedApp.patient?.lastname }} {{ selectedApp.patient?.firstname }}</p>
                    <p><strong>Service:</strong> {{ selectedApp.medical_service?.name }}</p>
                    <p><strong>Date:</strong> {{ formatDate(selectedApp.appointment_date) }} à {{ formatTime(selectedApp.appointment_time) }}</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Choisir le médecin idéal <span class="text-danger">*</span></label>
                    <div class="doctor-list">
                        <div v-for="doctor in filteredDoctors" :key="doctor.id"
                            class="doctor-option"
                            :class="{ selected: assignForm.doctor_id === doctor.id }"
                            @click="assignForm.doctor_id = doctor.id">
                            <div class="doctor-avatar">
                                <img :src="doctor.user?.avatar_url || '/assets/img/user.jpg'" />
                            </div>
                            <div class="doctor-info">
                                <div class="doctor-name">Dr. {{ doctor.user?.lastname }} {{ doctor.user?.firstname }} ({{ doctor.user?.phone || 'Pas de tél' }})</div>
                                <div class="doctor-specialty small text-muted">{{ doctor.specialty?.name }}</div>
                            </div>
                            <div class="check-icon" v-if="assignForm.doctor_id === doctor.id">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div v-if="filteredDoctors.length === 0" class="alert alert-warning py-2 small">
                        Aucun médecin disponible pour ce service.
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label">Notes de réception</label>
                    <textarea v-model="assignForm.notes" class="form-control" rows="3" placeholder="Informations complémentaires..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="closeModal" class="btn btn-outline-secondary">Annuler</button>
                <button @click="submitAssignment" class="btn btn-primary" :disabled="!assignForm.doctor_id || assignForm.processing">
                    {{ assignForm.processing ? 'Affectation...' : 'Confirmer l\'affectation' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import DatePickerComponent from '../../components/DatePickerComponent.vue';

const props = defineProps({
    appointments: Array,
    stats: Object,
    availableDoctors: Array,
    isDoctor: Boolean,
});

const toast = useToast();
const currentTab = ref('all');
const searchQuery = ref('');
const dateFilter = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const selectedApp = ref(null);
const currentPage = ref(1);
const perPage = 10;

const assignForm = useForm({
    doctor_id: '',
    notes: ''
});

// ── Computed: today's date string (YYYY-MM-DD) ──
const todayStr = new Date().toISOString().split('T')[0];

// ── Count of today's appointments ──
const todayCount = computed(() =>
    props.appointments.filter(a => a.appointment_date?.startsWith(todayStr)).length
);

// ── All appointments after tab filter ──
const allAppointments = computed(() => {
    return props.appointments.filter(a => {
        if (currentTab.value === 'requests')  return !a.doctor_id && a.status === 'PENDING';
        if (currentTab.value === 'scheduled') return a.doctor_id && ['PENDING', 'CONFIRMED'].includes(a.status);
        if (currentTab.value === 'history')   return ['COMPLETED', 'CANCELLED'].includes(a.status);
        if (currentTab.value === 'today')     return a.appointment_date?.startsWith(todayStr);
        return true; // 'all'
    });
});

// ── Computed: filtered appointments after search, date and status ──
const filteredAppointments = computed(() => {
    return allAppointments.value.filter(a => {
        if (dateFilter.value && !a.appointment_date?.startsWith(dateFilter.value)) return false;
        if (statusFilter.value && a.status !== statusFilter.value) return false;
        if (searchQuery.value) {
            const search = searchQuery.value.toLowerCase();
            const fullName = `${a.patient?.firstname ?? ''} ${a.patient?.lastname ?? ''}`.toLowerCase();
            if (!fullName.includes(search)) return false;
        }
        return true;
    });
});

// ── Pagination ──
const totalPages = computed(() => Math.ceil(filteredAppointments.value.length / perPage));
const paginationFrom = computed(() => Math.min((currentPage.value - 1) * perPage + 1, filteredAppointments.value.length));
const paginationTo   = computed(() => Math.min(currentPage.value * perPage, filteredAppointments.value.length));
const paginatedAppointments = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredAppointments.value.slice(start, start + perPage);
});

// Reset page when filters change
watch([filteredAppointments], () => { currentPage.value = 1; });

// ── Has active filters ──
const hasActiveFilters = computed(() =>
    !!dateFilter.value || !!statusFilter.value || !!searchQuery.value
);

// ── Set today filter ──
function setToday() {
    currentTab.value = 'today';
    dateFilter.value = '';
}

// ── Reset all filters ──
function resetFilters() {
    searchQuery.value = '';
    dateFilter.value = '';
    statusFilter.value = '';
}

// ── Doctors filtered by service and availability ──
const filteredDoctors = computed(() => {
    if (!selectedApp.value) return [];
    
    const appDate = selectedApp.value.appointment_date;
    const appTime = selectedApp.value.appointment_time.substring(0, 5); // HH:MM
    
    // Day of week
    const daysMap = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const dateObj = new Date(appDate);
    const dayKey = daysMap[dateObj.getDay()];

    return props.availableDoctors.filter(d => {
        // Filter by service
        const hasService = d.medical_service_id === selectedApp.value.medical_service_id;
        if (!hasService) return false;

        // Filter by availability (Planning)
        if (!d.availability || !d.availability[dayKey] || !d.availability[dayKey].enabled) {
            return false;
        }

        const slots = d.availability[dayKey].slots;
        if (!slots || slots.length === 0) return false;

        // Check if appointment time falls within any of the doctor's slots
        return slots.some(slot => {
            return appTime >= slot.start && appTime <= slot.end;
        });
    });
});

function openAssignModal(app) {
    selectedApp.value = app;
    showModal.value = true;
    assignForm.doctor_id = '';
}

function closeModal() {
    showModal.value = false;
    selectedApp.value = null;
}

function submitAssignment() {
    assignForm.post(route('appointments.assign', selectedApp.value.id), {
        onSuccess: () => {
            toast.success('Médecin affecté avec succès');
            closeModal();
        }
    });
}

function formatDate(date) {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatTime(time) {
    return time ? time.substring(0, 5) : '—';
}

function translateStatus(status) {
    const statuses = {
        'PENDING':   'En attente',
        'CONFIRMED': 'Confirmé',
        'COMPLETED': 'Terminé',
        'CANCELLED': 'Annulé',
        'POSTPONED': 'Reporté'
    };
    return statuses[status] || status;
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$secondary-color: #3f4254;
$success-color: #0abb87;
$warning-color: #ff9f43;
$danger-color: #f64e60;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 0.75rem;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }

.page-header {
    margin-bottom: 2rem;
    .header-content { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; }
    .page-title { font-weight: 700; color: #181c32; }
}

.filters-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;

    .filter-icon { color: #a1a5b7; font-size: 0.85rem; }

    .form-select, :deep(.form-control) {
        height: 38px;
        border-radius: 8px;
        border: 1px solid $border-color;
        font-size: 0.875rem;
        background: white;
        &:focus { border-color: $primary-color; box-shadow: 0 0 0 3px rgba($primary-color, 0.1); }
    }
    .form-select { min-width: 160px; }
}

.date-filter {
    position: relative;
    :deep(.form-control) { padding-right: 2rem; min-width: 160px; }
}

.clear-btn {
    background: none; border: none; color: #a1a5b7; cursor: pointer; padding: 0; margin-left: -1.5rem; z-index: 1;
    &:hover { color: $danger-color; }
}

.search-container {
    position: relative; width: 220px;
    .form-control { padding-left: 2.5rem; border-radius: 8px; }
    .search-icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; }
}

.btn-reset {
    background: #fef2f2; border: 1px solid #fecaca; color: $danger-color;
    padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
    &:hover { background: $danger-color; color: white; }
}

/* Filter summary */
.filter-summary {
    background: rgba($primary-color, 0.06); border: 1px solid rgba($primary-color, 0.15);
    padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; color: #374151;
}

/* Tabs Design */
.tabs-container {
    .premium-tabs {
        display: flex; gap: 0.5rem; background: #eaecf0; padding: 0.4rem; border-radius: 12px; display: inline-flex; flex-wrap: wrap;
        .tab-item {
            padding: 0.6rem 1.25rem; border: none; background: transparent; border-radius: 10px; font-weight: 600; color: #667085; transition: all 0.2s;
            &.active { background: white; color: $primary-color; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
            .badge { background: $danger-color; color: white; font-size: 0.7rem; border-radius: 50px; padding: 0.2rem 0.5rem; }
            .badge-outline { background: rgba($primary-color, 0.1); color: $primary-color; font-size: 0.7rem; border-radius: 50px; padding: 0.2rem 0.5rem; }
            .badge-count { background: #e5e7eb; color: #374151; font-size: 0.7rem; border-radius: 50px; padding: 0.2rem 0.5rem; }
            .badge-today { background: rgba($success-color, 0.15); color: $success-color; font-size: 0.7rem; border-radius: 50px; padding: 0.2rem 0.5rem; }
        }
    }
}

.data-card { background-color: white; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05); border: none; .card-body { padding: 0; } }

.custom-table {
    width: 100%;
    th { padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; font-weight: 600; color: $secondary-color; background-color: #fcfcfd; }
    td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid $border-color; }
}

.service-badge { font-weight: 600; color: $primary-color; background: rgba($primary-color, 0.08); padding: 0.25rem 0.75rem; border-radius: 6px; font-size: 0.85rem; display: inline-block; }

.status-badge {
    padding: 0.3rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    &.pending   { background-color: rgba($warning-color, 0.1); color: $warning-color; }
    &.confirmed { background-color: rgba($primary-color, 0.1); color: $primary-color; }
    &.completed { background-color: rgba($success-color, 0.1); color: $success-color; }
    &.cancelled { background-color: rgba($danger-color,  0.1); color: $danger-color;  }
}

.btn-primary-gradient {
    background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); color: white; border: none; font-weight: 600; border-radius: 8px;
    &:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba($primary-color, 0.3); }
}

.empty-state { i { display: block; margin-bottom: 1rem; } }

/* Modal */
.modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;
    .modal-content {
        background: white; width: 500px; border-radius: 16px; overflow: hidden; animation: slideUp 0.3s ease-out;
        .modal-header { padding: 1.5rem; border-bottom: 1px solid $border-color; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { padding: 1.25rem; border-top: 1px solid $border-color; display: flex; justify-content: flex-end; gap: 0.75rem; }
    }
}

.doctor-list {
    max-height: 250px; overflow-y: auto; border: 1px solid $border-color; border-radius: 10px;
    .doctor-option {
        display: flex; align-items: center; padding: 0.75rem; border-bottom: 1px solid #f1f1f1; cursor: pointer; transition: all 0.2s;
        &:last-child { border-bottom: none; }
        &:hover { background: #f8f9ff; }
        &.selected { background: rgba($primary-color, 0.05); border-left: 4px solid $primary-color; }
        .doctor-avatar { width: 35px; height: 35px; border-radius: 50%; overflow: hidden; margin-right: 0.75rem; img { width: 100%; height: 100%; object-fit: cover; } }
        .doctor-name { font-weight: 600; color: #181c32; }
        .check-icon { margin-left: auto; color: $primary-color; font-size: 1.2rem; }
    }
}

@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid $border-color;

    .pagination-info { font-size: 0.875rem; color: #7e8299; }

    .pagination {
        margin: 0;
        display: flex;
        gap: 4px;

        .page-item.active .page-link {
            background: $primary-color;
            border-color: $primary-color;
            color: white;
        }
        .page-item.disabled .page-link { opacity: 0.5; cursor: not-allowed; }

        .page-link {
            border-radius: 6px;
            border: 1px solid $border-color;
            color: $primary-color;
            background: white;
            cursor: pointer;
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
            &:hover { background: rgba($primary-color, 0.08); }
        }
    }
}
</style>
