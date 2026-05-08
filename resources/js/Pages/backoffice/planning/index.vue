<template>
    <Head>
        <title>Planning Global | CMC</title>
    </Head>

    <div class="content container-fluid">

        <!-- Planning Header Banner -->
        <div class="planning-banner mb-4">
            <div class="banner-content">
                <div class="banner-info">
                    <h1 class="h3 fw-bold mb-1">Planning Global</h1>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-hospital-user me-2"></i>
                        <span v-if="selectedDoctorId === 'all'">Vue d'ensemble de l'hôpital</span>
                        <span v-else>Dr. {{ selectedDoctorData?.user?.lastname }} {{ selectedDoctorData?.user?.firstname }}</span>
                    </p>
                </div>
                
                <div class="banner-controls">
                    <!-- Navigation -->
                    <div class="nav-group me-3">
                        <button class="nav-btn" @click="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                        <div class="nav-display d-flex gap-2">
                            <select v-model="currentMonth" @change="fetchPlanning(currentMonth, currentYear)" class="filter-select border-0 fw-bold">
                                <option v-for="(name, idx) in monthNames" :key="idx" :value="idx">{{ name }}</option>
                            </select>
                            <select v-model="currentYear" @change="fetchPlanning(currentMonth, currentYear)" class="filter-select border-0">
                                <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <button class="nav-btn" @click="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    
                    <!-- Doctor Filter -->
                    <div class="filter-group" v-if="!isDoctor">
                        <select v-model="selectedDoctorId" class="filter-select">
                            <option value="all">Tous les médecins</option>
                            <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                                Dr. {{ doc.user.lastname }} {{ doc.user.firstname }}
                            </option>
                        </select>
                    </div>

                    <button class="today-btn ms-2" @click="goToToday">Aujourd'hui</button>
                </div>
            </div>
            <div class="banner-pattern"></div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="stat-card worked">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ stats.confirmed }}</div>
                        <div class="stat-label">{{ translateStatus('CONFIRMED') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card missed">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="stat-value">{{ stats.pending }}</div>
                        <div class="stat-label">{{ translateStatus('PENDING') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card off">
                    <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                    <div>
                        <div class="stat-value">{{ stats.completed }}</div>
                        <div class="stat-label">{{ translateStatus('COMPLETED') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card noshow-card">
                    <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
                    <div>
                        <div class="stat-value">{{ stats.noShow }}</div>
                        <div class="stat-label">{{ translateStatus('NO_SHOW') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card noSchedule">
                    <div class="stat-icon"><i class="fas fa-list"></i></div>
                    <div>
                        <div class="stat-value">{{ stats.total }}</div>
                        <div class="stat-label">Total RDV</div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Calendar Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">

                <!-- Day labels -->
                <div class="cal-grid">
                    <div v-for="d in dayLabels" :key="d" class="cal-day-label">{{ d }}</div>
                </div>

                <!-- Days grid -->
                <div class="cal-grid mt-1">
                    <!-- Empty cells before first day -->
                    <div v-for="n in leadingBlanks" :key="'b' + n" class="cal-cell blank"></div>

                    <!-- Actual day cells -->
                    <div
                        v-for="day in calendarDays"
                        :key="day.dateStr"
                        class="cal-cell"
                        :class="[day.statusColor, { today: day.isToday, 'is-weekend': day.isWeekend }]"
                        @click="day.hasAppointments && openDayModal(day)"
                        :title="day.tooltip"
                    >
                        <div class="day-number">{{ day.dayNum }}</div>

                        <!-- Patient names preview -->
                        <div v-if="day.appointments.length > 0" class="day-houses mt-1">
                            <div
                                v-for="(app, i) in day.appointments.slice(0, 3)"
                                :key="i"
                                class="day-house"
                                :class="getApptStatusClass(app.status)"
                            >
                                <div class="day-house-row">
                                    <span class="appt-time">{{ formatTime(app.appointment_time) }}</span>
                                    <span class="appt-patient">{{ app.patient?.firstname }} {{ app.patient?.lastname }}</span>
                                </div>
                                <div class="appt-doctor" v-if="app.doctor">
                                    <i class="fas fa-user-md"></i> Dr. {{ app.doctor?.lastname }}
                                </div>
                            </div>
                        </div>

                        <!-- Status icon bottom-right -->
                        <div class="day-status-icon">
                            <i v-if="day.statusColor === 'noshow'" class="fas fa-user-slash text-danger"></i>
                            <i v-else-if="day.statusColor === 'missed'" class="fas fa-clock text-warning"></i>
                            <i v-else-if="day.statusColor === 'worked'" class="fas fa-check-circle text-success"></i>
                            <i v-else-if="day.statusColor === 'off'" class="fas fa-check-double text-primary"></i>
                        </div>

                        <!-- RDV count badge -->
                        <div v-if="day.appointments.length > 1" class="shifts-badge">{{ day.appointments.length }}</div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center legend">
                    <span class="legend-item worked"><i class="fas fa-check-circle me-1"></i>{{ translateStatus('CONFIRMED') }}</span>
                    <span class="legend-item missed"><i class="fas fa-clock me-1"></i>{{ translateStatus('PENDING') }}</span>
                    <span class="legend-item off"><i class="fas fa-check-double me-1"></i>{{ translateStatus('COMPLETED') }}</span>
                    <span class="legend-item noshow"><i class="fas fa-user-slash me-1"></i>{{ translateStatus('NO_SHOW') }}</span>
                    <span class="legend-item noSchedule"><i class="fas fa-minus me-1"></i>Aucun RDV</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Day Detail Modal -->
    <Transition name="fade">
        <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
            <div class="detail-modal">
                <div class="detail-header">
                    <span>{{ formatDateFull(modalData.dateStr) }}</span>
                    <button class="btn-close" @click="showModal = false"></button>
                </div>
                <div class="detail-body">
                    <div v-if="!modalData.appointments || modalData.appointments.length === 0" class="text-center text-muted py-4">
                        Aucun rendez-vous ce jour-là.
                    </div>
                    <div v-for="(app, i) in modalData.appointments" :key="i" class="shift-block mb-3">
                        <!-- Status + heure -->
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge" :class="getStatusBadgeClass(app.status)">
                                {{ translateStatus(app.status) }}
                            </span>
                            <span class="text-muted small">{{ formatTime(app.appointment_time) }}</span>
                        </div>
                        <!-- Patient -->
                        <div class="fw-bold">{{ app.patient?.firstname }} {{ app.patient?.lastname }}</div>
                        <!-- Médecin -->
                        <div class="small text-muted" v-if="app.doctor">
                            <i class="fas fa-user-md me-1"></i> Dr. {{ app.doctor?.lastname }}
                        </div>
                        <!-- Bouton No-Show (seulement pour CONFIRMED) -->
                        <div v-if="app.status === 'CONFIRMED'" class="mt-2">
                            <div v-if="noShowLoading" class="text-muted small">Traitement...</div>
                            <div v-else>
                                <textarea
                                    v-model="noShowNotes[app.id]"
                                    class="form-control form-control-sm mb-1"
                                    rows="2"
                                    placeholder="Motif de l'absence (obligatoire)..."
                                ></textarea>
                                <button
                                    class="btn btn-sm btn-danger w-100"
                                    @click="markNoShow(app)"
                                >
                                    <i class="fas fa-user-slash me-1"></i> Marquer comme absent
                                </button>
                            </div>
                        </div>
                        <!-- Badge no-show déjà marqué -->
                        <div v-if="app.status === 'NO_SHOW'" class="mt-1">
                            <span class="badge bg-dark"><i class="fas fa-user-slash me-1"></i>Patient absent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import { format, isSameDay, parseISO, getDaysInMonth } from 'date-fns';
import { fr } from 'date-fns/locale';

const props = defineProps({
    doctors:     Array,
    appointments: Object,   // { doctor_user_id: { 'yyyy-mm-dd': [appointments] } }
    services:    Array,
    isDoctor:    Boolean,
    initialDate: String,
});

const toast = useToast();

// ── State ──────────────────────────────────────────────────────────────────
const today            = new Date();
const currentMonth     = ref(new URLSearchParams(window.location.search).get('month') ? parseInt(new URLSearchParams(window.location.search).get('month')) - 1 : today.getMonth());
const currentYear      = ref(new URLSearchParams(window.location.search).get('year') ? parseInt(new URLSearchParams(window.location.search).get('year')) : today.getFullYear());
const selectedDoctorId = ref('all');
const showModal        = ref(false);
const noShowNotes      = ref({});
const noShowLoading    = ref(false);
const modalData        = ref({ dateStr: '', appointments: [] });

const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
const dayLabels  = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

const yearOptions = computed(() => {
    const y = today.getFullYear();
    return Array.from({ length: 5 }, (_, i) => y - 2 + i);
});

// Doctor selected data (for header display)
const selectedDoctorData = computed(() => {
    if (selectedDoctorId.value === 'all') return null;
    return props.doctors.find(d => d.id == selectedDoctorId.value) ?? null;
});

// If user is a doctor, auto-select their profile
watch(() => props.isDoctor, (val) => {
    if (val && props.doctors.length === 1) {
        selectedDoctorId.value = props.doctors[0].id;
    }
}, { immediate: true });

// ── Navigation ─────────────────────────────────────────────────────────────
const fetchPlanning = (m, y) => {
    router.get(route('planning.index'), {
        month: parseInt(m) + 1, // backend expects 1-12
        year: y
    }, { 
        preserveState: true, 
        preserveScroll: true,
        onSuccess: () => {
            currentMonth.value = parseInt(m);
            currentYear.value = parseInt(y);
        }
    });
};

const changeMonth = (offset) => {
    const d = new Date(currentYear.value, currentMonth.value + offset, 1);
    fetchPlanning(d.getMonth(), d.getFullYear());
};
const goToToday = () => {
    fetchPlanning(today.getMonth(), today.getFullYear());
};

// ── Calendar helpers ───────────────────────────────────────────────────────
const leadingBlanks = computed(() =>
    new Date(currentYear.value, currentMonth.value, 1).getDay()
);

/**
 * Get appointments for a specific date, respecting the doctor filter.
 * props.appointments structure: { [doctor_user_id]: { [yyyy-mm-dd]: [...] } }
 */
function getAppointmentsForDate(dateStr) {
    let apps = [];

    if (selectedDoctorId.value === 'all') {
        // Merge appointments from all doctors
        props.doctors.forEach(doc => {
            const byDate = props.appointments?.[doc.user_id];
            if (byDate) {
                const dayApps = byDate[dateStr] ?? [];
                apps = [...apps, ...dayApps];
            }
        });
    } else {
        const doctor = props.doctors.find(d => d.id == selectedDoctorId.value);
        if (doctor) {
            const byDate = props.appointments?.[doctor.user_id];
            apps = byDate?.[dateStr] ?? [];
        }
    }

    // Sort by appointment_time
    apps.sort((a, b) => (a.appointment_time ?? '').localeCompare(b.appointment_time ?? ''));
    return apps;
}

const calendarDays = computed(() => {
    const daysInMonth = getDaysInMonth(new Date(currentYear.value, currentMonth.value));
    const days = [];

    for (let d = 1; d <= daysInMonth; d++) {
        const dateObj    = new Date(currentYear.value, currentMonth.value, d);
        const dateStr    = format(dateObj, 'yyyy-MM-dd');
        const dayOfWeek  = dateObj.getDay();
        const isTodayDay = isSameDay(dateObj, today);
        const isWeekend  = dayOfWeek === 0 || dayOfWeek === 6;

        const appointments = getAppointmentsForDate(dateStr);

        // Status colour logic (Priorities: NO_SHOW > PENDING > CONFIRMED > COMPLETED)
        let statusColor = 'noSchedule';
        if (appointments.length > 0) {
            const hasNoShow    = appointments.some(a => a.status === 'NO_SHOW');
            const hasPending   = appointments.some(a => a.status === 'PENDING');
            const hasConfirmed = appointments.some(a => a.status === 'CONFIRMED');
            const hasCompleted = appointments.some(a => a.status === 'COMPLETED');

            if (hasNoShow)          statusColor = 'noshow';
            else if (hasPending)    statusColor = 'missed';
            else if (hasConfirmed)  statusColor = 'worked';
            else if (hasCompleted)  statusColor = 'off';
            else statusColor = 'off'; // Default for other statuses like CANCELLED
        }

        const firstConfirmed = appointments.find(a => a.status === 'CONFIRMED');
        const firstPending   = appointments.find(a => a.status === 'PENDING');

        days.push({
            dayNum:              d,
            dateStr,
            appointments,
            isToday:             isTodayDay,
            isWeekend,
            statusColor,
            hasAppointments:     appointments.length > 0,
            hasConfirmed:        !!firstConfirmed,
            firstConfirmedTime:  firstConfirmed ? formatTime(firstConfirmed.appointment_time) : '',
            hasPending:          !!firstPending,
            firstPendingTime:    firstPending ? formatTime(firstPending.appointment_time) : '',
            tooltip:             appointments.length > 0 ? `${appointments.length} RDV` : 'Aucun RDV',
        });
    }
    return days;
});

// ── Stats ──────────────────────────────────────────────────────────────────
const stats = computed(() => {
    let confirmed = 0, pending = 0, completed = 0, noShow = 0, total = 0;
    calendarDays.value.forEach(day => {
        day.appointments.forEach(app => {
            total++;
            if (app.status === 'CONFIRMED')       confirmed++;
            else if (app.status === 'PENDING')    pending++;
            else if (app.status === 'COMPLETED')  completed++;
            else if (app.status === 'NO_SHOW')    noShow++;
        });
    });
    return { confirmed, pending, completed, noShow, total };
});

// ── Modal & Helpers ────────────────────────────────────────────────────────
const openDayModal = (day) => {
    modalData.value = day;
    noShowNotes.value = {};
    showModal.value = true;
};

const markNoShow = (app) => {
    const note = noShowNotes.value[app.id] || '';
    if (!note.trim()) {
        toast.error('Veuillez indiquer le motif de l\'absence.');
        return;
    }
    noShowLoading.value = true;
    router.put(
        route('appointments.update-status', app.uuid ?? app.id),
        { status: 'NO_SHOW', notes: note },
        {
            preserveScroll: true,
            onSuccess: () => {
                noShowNotes.value[app.id] = '';
                showModal.value = false;
            },
            onFinish: () => { noShowLoading.value = false; },
        }
    );
};

const formatTime     = (t) => t ? String(t).substring(0, 5) : '';
const formatDateFull = (dateStr) => {
    try { return format(parseISO(dateStr), 'EEEE d MMMM yyyy', { locale: fr }); }
    catch { return dateStr; }
};

function translateStatus(status) {
    const statuses = {
        'PENDING':   'En attente',
        'CONFIRMED': 'Confirmé',
        'COMPLETED': 'Terminé',
        'CANCELLED': 'Annulé',
        'POSTPONED': 'Reporté',
        'NO_SHOW':   'Absent'
    };
    return statuses[status] || status;
}
const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'CONFIRMED':  return 'bg-success text-white';
        case 'PENDING':    return 'bg-warning text-dark';
        case 'COMPLETED':  return 'bg-primary text-white';
        case 'NO_SHOW':    return 'bg-danger text-white';
        default:           return 'bg-dark text-white';
    }
};

const getApptStatusClass = (status) => {
    switch (status) {
        case 'CONFIRMED':  return 'appt-confirmed';
        case 'PENDING':    return 'appt-pending';
        case 'COMPLETED':  return 'appt-completed';
        case 'NO_SHOW':    return 'appt-noshow';
        default:           return 'appt-default';
    }
};
</script>

<style lang="scss" scoped>
// ── Stats Cards ─────────────────────────────────────────────────────────────
.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-left: 4px solid transparent;
    transition: transform 0.2s;

    &:hover { transform: translateY(-3px); }

    &.worked     { border-left-color: #10b981; }
    &.missed     { border-left-color: #f59e0b; }
    &.off        { border-left-color: #3b82f6; }
    &.noshow-card { border-left-color: #ef4444; }
    &.noSchedule { border-left-color: #94a3b8; }
}

.stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    background: #f8fafc;
    color: #64748b;
}

.worked     .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.missed     .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.off        .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.noshow-card .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.stat-value { font-size: 1.25rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
.stat-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; }

// ── Planning Banner ──────────────────────────────────────────────────────────
.planning-banner {
    background: #1e293b;
    border-radius: 1.25rem;
    padding: 1.5rem 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.banner-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.banner-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem;
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    padding-right: 0.75rem;
}

.nav-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
    transition: all 0.2s;
    &:hover { background: #6366f1; }
}

.nav-display {
    text-align: center;
    min-width: 100px;
}

.month-name { display: block; font-weight: 700; font-size: 0.9rem; }
.year-num { display: block; font-size: 0.7rem; opacity: 0.7; }

.filter-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.4rem 0.75rem;
    border-radius: 0.75rem;
    font-size: 0.85rem;
    outline: none;
    cursor: pointer;
    &:hover { background: rgba(255, 255, 255, 0.15); }
    option { color: #1e293b; }
}

.today-btn {
    background: #6366f1;
    color: white;
    border: none;
    padding: 0.4rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
    &:hover { background: #4f46e5; transform: translateY(-1px); }
}

.banner-pattern {
    position: absolute;
    top: 0; right: 0; width: 30%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03));
    mask-image: radial-gradient(circle at center, black 1px, transparent 1px);
    mask-size: 15px 15px;
}

// ── Calendar Grid ────────────────────────────────────────────────────────────
.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.cal-day-label {
    text-align: center;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 10px 0;
}

.cal-cell {
    background: white;
    border-radius: 0.75rem;
    padding: 0.75rem;
    min-height: 125px;
    border: 1px solid #f1f5f9;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    cursor: pointer;

    &:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #e2e8f0;
        z-index: 10;
    }

    &.blank { background: #f8fafc; cursor: default; border: none; &:hover { transform: none; box-shadow: none; } }
    
    &.today {
        background: #f0f9ff;
        border: 2px solid #3b82f6;
        .day-number { color: #3b82f6; font-weight: 800; }
    }

    &.worked { border-left: 3px solid #22c55e; }
    &.missed { border-left: 3px solid #f59e0b; }
    &.noshow { border-left: 3px solid #ef4444; }
    &.off    { border-left: 3px solid #94a3b8; }
    
    &.is-weekend { background-color: #f8fafc; }
}

.day-number {
    font-size: 1.05rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 0.25rem;
}

.day-houses {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.day-house {
    background: white;
    padding: 4px 6px;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border: 1px solid #f1f5f9;
    border-left: 3px solid transparent;
    
    &.appt-confirmed { border-left-color: #10b981; .appt-time { color: #10b981; } }
    &.appt-pending   { border-left-color: #f59e0b; .appt-time { color: #f59e0b; } }
    &.appt-completed { border-left-color: #3b82f6; .appt-time { color: #3b82f6; } }
    &.appt-noshow    { border-left-color: #ef4444; .appt-time { color: #ef4444; text-decoration: line-through; } }
    &.appt-default   { border-left-color: #94a3b8; .appt-time { color: #94a3b8; } }
}

.day-house-row {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
}

.appt-time {
    font-weight: 700;
    font-size: 0.65rem;
}

.appt-patient {
    font-size: 0.65rem;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.appt-doctor {
    font-size: 0.55rem;
    font-weight: 500;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-left: 2px;
    margin-top: 1px;
    i { font-size: 0.5rem; color: #94a3b8; }
}

// ── Status icon (bottom-right of cell) ───────────────────────────────────
.day-status-icon {
    position: absolute;
    bottom: 6px;
    right: 6px;
    font-size: 0.8rem;
}

// ── Count badge (top-right of cell) ──────────────────────────────────────
.shifts-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #6366f1;
    color: #fff;
    font-size: 0.6rem;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 99px;
}

// ── Legend ────────────────────────────────────────────────────────────────
.legend-item {
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 99px;
    font-weight: 600;
    display: flex;
    align-items: center;
    &.worked     { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
    &.missed     { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
    &.off        { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    &.noshow     { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    &.noSchedule { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
}

// ── Modal ─────────────────────────────────────────────────────────────────
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.detail-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);

    .detail-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .detail-body {
        padding: 1rem 1.25rem;
        max-height: 60vh;
        overflow-y: auto;
    }

    .shift-block {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        background: #fafafa;
    }
}

.btn-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>