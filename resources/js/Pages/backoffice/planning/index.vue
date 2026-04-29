<template>
    <Head>
        <title>Planning Global | CMC</title>
    </Head>

    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="header-content d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Planning Global Hospitalier
                        </h4>
                        <p class="text-muted mb-0">Visualisez les disponibilités et rendez-vous de toute l'équipe.</p>
                    </div>
                    
                    <div class="week-selector d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm border">
                        <button @click="changeWeek(-1)" class="btn btn-icon btn-light rounded-circle" title="Semaine précédente">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <div class="week-info d-flex align-items-center px-3">
                            <span class="fw-bold text-dark week-display me-2">{{ currentWeek.display }}</span>
                            <div class="date-picker-jump">
                                <label for="jump-date" class="cursor-pointer mb-0" title="Choisir une date spécifique">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </label>
                                <DatePicker 
                                    id="jump-date"
                                    v-model="filters.date" 
                                    :enableTime="false" 
                                    class="hidden-picker"
                                    @update:modelValue="submitSearch"
                                />
                            </div>
                        </div>

                        <button @click="changeWeek(1)" class="btn btn-icon btn-light rounded-circle" title="Semaine suivante">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button @click="resetWeek" class="btn btn-sm btn-outline-primary rounded-pill px-3 ms-2">Aujourd'hui</button>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm border-0 mb-4 rounded-4" v-if="!isDoctor">
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" v-model="filters.search" placeholder="Rechercher un médecin..." class="form-control ps-5 rounded-pill border-light bg-light" @input="debouncedSearch" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select v-model="filters.service_id" class="form-select rounded-pill border-light bg-light" @change="submitSearch">
                                <option value="">Tous les services</option>
                                <option v-for="service in services" :key="service.id" :value="service.id">
                                    {{ service.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select v-model="filters.per_page" class="form-select rounded-pill border-light bg-light" @change="submitSearch">
                                <option value="10">10 par page</option>
                                <option value="20">20 par page</option>
                                <option value="50">50 par page</option>
                                <option value="100">100 par page</option>
                                <option value="all">Tout afficher</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Planning Grid -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="planning-table-container">
                    <table class="table planning-table mb-0">
                        <thead>
                            <tr>
                                <th class="sticky-col first-col">Médecin</th>
                                <th v-for="day in weekDays" :key="day.key" class="text-center day-header" :class="{ 'is-today': isToday(day.date) }">
                                    <div class="day-name">{{ day.name }}</div>
                                    <div class="day-date">{{ formatDateShort(day.date) }}</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="doctor in doctors.data" :key="doctor.id">
                                <td class="sticky-col first-col">
                                    <div class="doctor-info-cell d-flex align-items-center gap-3">
                                        <div class="avatar-wrapper">
                                            <img :src="doctor.user.avatar_url || '/assets/img/user.jpg'" alt="Avatar" class="doctor-avatar" />
                                        </div>
                                        <div class="details">
                                            <div class="name fw-bold">Dr. {{ doctor.user.lastname }}</div>
                                            <div class="service small text-muted">{{ doctor.medical_service?.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td v-for="day in weekDays" :key="day.key" class="day-cell" :class="{ 'is-today': isToday(day.date) }">
                                    <!-- Disponibilités (Background) -->
                                    <div v-if="isDoctorAvailable(doctor, day.key)" class="availability-zones">
                                        <div v-for="(slot, idx) in getDoctorSlots(doctor, day.key)" :key="idx" class="slot-indicator">
                                            <i class="far fa-clock me-1"></i> {{ slot.start }} - {{ slot.end }}
                                        </div>
                                    </div>
                                    <div v-else class="closed-indicator">
                                        <span class="badge bg-light text-muted fw-normal">Indisponible</span>
                                    </div>

                                    <!-- Rendez-vous (Foreground) -->
                                    <div class="appointments-container mt-2">
                                        <div v-for="app in getAppointments(doctor, day.date)" :key="app.id" 
                                            class="appointment-card" 
                                            :class="app.status.toLowerCase()"
                                            @click="viewAppointment(app.uuid)">
                                            <div class="app-time">{{ formatTime(app.appointment_time) }}</div>
                                            <div class="app-patient text-truncate">{{ app.patient.firstname }} {{ app.patient.lastname }}</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination-container d-flex justify-content-between align-items-center mt-4 px-2" v-if="doctors.links && doctors.links.length > 3">
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        <li v-for="(link, index) in doctors.links" :key="index" class="page-item"
                            :class="{ 'active': link.active, 'disabled': !link.url }">
                            <button class="page-link" 
                                    @click="link.url ? router.get(link.url, filters, { preserveState: true }) : null" 
                                    v-html="link.label"
                                    :disabled="!link.url">
                            </button>
                        </li>
                    </ul>
                </nav>
                <div class="pagination-info">
                    Affichage de <span class="fw-medium">{{ doctors.from }}</span> à <span class="fw-medium">{{ doctors.to }}</span> sur <span class="fw-medium">{{ doctors.total }}</span> entrées
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { format, addDays, startOfWeek, isSameDay, parseISO } from 'date-fns';
import { fr } from 'date-fns/locale';
import debounce from 'lodash/debounce';
import DatePicker from '../../components/DatePicker.vue';

const props = defineProps({
    doctors: Object, // Changé de Array à Object pour la pagination
    appointments: Object, // Groupé par [doctor_id][date]
    services: Array,
    currentWeek: Object,
    filters: Object,
    isDoctor: Boolean,
});

const filters = ref({
    search: props.filters.search || '',
    service_id: props.filters.service_id || '',
    date: props.filters.date || props.currentWeek.start,
    per_page: props.filters.per_page || '10',
});

// Calcul des jours de la semaine
const weekDays = computed(() => {
    const start = parseISO(props.currentWeek.start);
    const days = [];
    const dayNames = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const dayLabels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    for (let i = 0; i < 7; i++) {
        const d = addDays(start, i);
        days.push({
            key: dayNames[i],
            name: dayLabels[i],
            date: format(d, 'yyyy-MM-dd'),
        });
    }
    return days;
});

const isToday = (dateStr) => isSameDay(parseISO(dateStr), new Date());

const formatDateShort = (dateStr) => format(parseISO(dateStr), 'dd MMM', { locale: fr });

const formatTime = (time) => time.substring(0, 5);

// Logique Disponibilités
const isDoctorAvailable = (doctor, dayKey) => {
    return doctor.availability && doctor.availability[dayKey] && doctor.availability[dayKey].enabled;
};

const getDoctorSlots = (doctor, dayKey) => {
    return doctor.availability[dayKey].slots || [];
};

// Logique Rendez-vous
const getAppointments = (doctor, date) => {
    if (!props.appointments[doctor.user_id]) return [];
    return props.appointments[doctor.user_id][date] || [];
};

// Actions
const viewAppointment = (uuid) => {
    router.get(route('appointments.show', uuid));
};

const changeWeek = (offset) => {
    const newDate = addDays(parseISO(props.currentWeek.start), offset * 7);
    filters.value.date = format(newDate, 'yyyy-MM-dd');
    submitSearch();
};

const resetWeek = () => {
    filters.value.date = format(new Date(), 'yyyy-MM-dd');
    submitSearch();
};

const submitSearch = () => {
    router.get(route('planning.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedSearch = debounce(submitSearch, 500);
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$success-color: #0abb87;
$warning-color: #ff9f43;
$danger-color: #f64e60;
$border-color: #f1f3f9;
$bg-light: #f8fafc;

$body-color: #f9fafb;

.content-wrapper {
    background-color: $body-color;
    min-height: 100vh;
    padding: 2rem 0;
}

.planning-table-container {
    overflow-x: auto;
    position: relative;
    max-height: calc(100vh - 250px);
}

.planning-table {
    border-collapse: separate;
    border-spacing: 0;
    
    th, td {
        padding: 1rem;
        border: 1px solid $border-color;
        min-width: 180px;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        background: white;
        z-index: 10;
        min-width: 240px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.02);
    }

    thead th {
        background: #fff;
        position: sticky;
        top: 0;
        z-index: 20;
        border-top: none;
        
        &.sticky-col { z-index: 30; }
    }
}

.day-header {
    &.is-today {
        background-color: rgba($primary-color, 0.03);
        .day-name { color: $primary-color; }
    }
    .day-name { font-weight: 700; font-size: 1rem; position: relative; z-index: 1; }
    .day-date { font-size: 0.8rem; color: #a1a5b7; position: relative; z-index: 1; }
}

.doctor-info-cell {
    .doctor-avatar { width: 42px; height: 42px; border-radius: 10px; object-fit: cover; }
    .name { color: #181c32; }
}

.day-cell {
    height: 100%;
    vertical-align: top;
    background-color: #fff;
    padding-top: 1.5rem !important; /* Ajout d'un espace pour éviter le chevauchement avec le header */
    &.is-today { background-color: rgba($primary-color, 0.01); }
}

.slot-indicator {
    font-size: 0.75rem;
    color: $primary-color;
    background: rgba($primary-color, 0.08);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.appointment-card {
    background: white;
    border: 1px solid #e4e6ef;
    border-left: 3px solid $primary-color;
    padding: 0.5rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }

    .app-time { font-weight: 700; font-size: 0.8rem; color: #181c32; }
    .app-patient { font-size: 0.75rem; color: #5e6278; }

    &.confirmed { border-left-color: $success-color; .app-time { color: $success-color; } }
    &.pending { border-left-color: $warning-color; .app-time { color: $warning-color; } }
    &.completed { border-left-color: #7e8299; background-color: #fcfcfd; }
}

.pagination-container {
    .pagination {
        gap: 0;
        .page-item {
            .page-link {
                border: 1px solid $border-color;
                color: #5e6278;
                padding: 0.5rem 0.85rem;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.2s ease;
                &:hover:not(.disabled) { background-color: #f1f3f9; color: $primary-color; }
            }
            &.active .page-link { background-color: $primary-color; border-color: $primary-color; color: white; }
            &.disabled .page-link { color: #b5b5c3; background-color: #f9fafb; cursor: not-allowed; }
            &:first-child .page-link { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
            &:last-child .page-link { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        }
    }
    .pagination-info { font-size: 0.875rem; color: #7e8299; .fw-medium { color: #3f4254; font-weight: 600; } }
}

.week-display { font-size: 0.95rem; }

.date-picker-jump {
    position: relative;
    .hidden-picker {
        position: absolute;
        width: 0;
        height: 0;
        opacity: 0;
        padding: 0;
        border: 0;
        pointer-events: none;
    }
}

.search-box {
    position: relative;
    .search-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; }
}
</style>
