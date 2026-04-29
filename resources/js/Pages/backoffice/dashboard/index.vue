<template>
    <Head>
        <title>Dashboard Hospitalier | CCS</title>
        <meta name="description" content="Tableau de bord de gestion hospitalière" />
    </Head>

    <div class="dashboard-wrapper">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1>Tableau de Bord Hospitalier</h1>
                <p>Bienvenue dans votre interface de gestion des rendez-vous et patients.</p>
            </div>
            <div class="header-actions">
                <div class="date-display">
                    <i class="far fa-calendar-alt"></i> {{ currentDateTime }}
                </div>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="stats-container">
            <div class="stat-card primary" @click="$inertia.visit(route('appointments.index'))">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h2>{{ props.stats.total_appointments }}</h2>
                    <p>TOTAL RENDEZ-VOUS</p>
                </div>
                <div class="stat-pending">
                    {{ props.stats.pending_appointments }} En attente
                </div>
            </div>

            <div class="stat-card success" @click="$inertia.visit(route('patients.index'))">
                <div class="stat-icon">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="stat-info">
                    <h2>{{ props.stats.patient_count }}</h2>
                    <p>PATIENTS ENREGISTRÉS</p>
                </div>
            </div>

            <div class="stat-card info" @click="$inertia.visit(route('doctors.index'))">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-info">
                    <h2>{{ props.stats.doctor_count }}</h2>
                    <p>MÉDECINS</p>
                </div>
            </div>

            <div class="stat-card warning" @click="$inertia.visit(route('medical-services.index'))">
                <div class="stat-icon">
                    <i class="fas fa-hospital-symbol"></i>
                </div>
                <div class="stat-info">
                    <h2>{{ props.stats.services_count }}</h2>
                    <p>SERVICES MÉDICAUX</p>
                </div>
                <div class="stat-pending">
                    {{ props.stats.active_services }} Actifs
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="dashboard-grid">
            <!-- Left Column: Today's Appointments -->
            <div class="grid-section">
                <div class="section-header">
                    <h2>Rendez-vous du Jour</h2>
                    <Link :href="route('appointments.index')" class="view-all">Voir tout</Link>
                </div>
                <div class="appointments-list">
                    <div v-if="props.todayAppointments.length === 0" class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <p>Aucun rendez-vous prévu pour aujourd'hui.</p>
                    </div>
                    <div v-else v-for="app in props.todayAppointments" :key="app.id" class="appointment-item">
                        <div class="app-time">{{ app.appointment_time }}</div>
                        <div class="app-details">
                            <div class="patient-name">{{ app.patient }}</div>
                            <div class="doctor-name">{{ app.doctor }} • {{ app.service }}</div>
                        </div>
                        <div class="app-status">
                            <span :class="'status-badge ' + app.status_key">
                                {{ app.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Appointment Status Distribution & Quick Actions -->
            <div class="grid-section">
                <div class="section-header">
                    <h2>Répartition des Rendez-vous</h2>
                </div>
                <div class="status-distribution">
                    <div class="dist-item">
                        <span class="label">Confirmés</span>
                        <div class="progress-container">
                            <div class="progress-bar confirmed" :style="{ width: getPercentage(props.stats.confirmed_appointments) + '%' }"></div>
                        </div>
                        <span class="value">{{ props.stats.confirmed_appointments }}</span>
                    </div>
                    <div class="dist-item">
                        <span class="label">Terminés</span>
                        <div class="progress-container">
                            <div class="progress-bar completed" :style="{ width: getPercentage(props.stats.completed_appointments) + '%' }"></div>
                        </div>
                        <span class="value">{{ props.stats.completed_appointments }}</span>
                    </div>
                    <div class="dist-item">
                        <span class="label">Annulés</span>
                        <div class="progress-container">
                            <div class="progress-bar cancelled" :style="{ width: getPercentage(props.stats.cancelled_appointments) + '%' }"></div>
                        </div>
                        <span class="value">{{ props.stats.cancelled_appointments }}</span>
                    </div>
                </div>

                <div class="quick-actions">
                    <h3>Actions Rapides</h3>
                    <div class="actions-grid">
                        <button @click="$inertia.visit(route('medical-services.index'))" class="action-btn">
                            <i class="fas fa-hospital-symbol"></i> Gérer les Services
                        </button>
                        <button @click="$inertia.visit(route('schedules.index'))" class="action-btn">
                            <i class="fas fa-calendar-alt"></i> Gérer les Disponibilités
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { userHasPermission } from "../../../utils/auth";
import { PERMISSIONS } from "../../../constants/permission";

const props = defineProps({
    stats: Object,
    todayAppointments: Array,
    permissionsMap: Object,
});

const currentDateTime = ref("");

const updateDateTime = () => {
    const now = new Date();
    currentDateTime.value = now.toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }) + " " + now.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getPercentage = (value) => {
    if (!props.stats.total_appointments) return 0;
    return Math.round((value / props.stats.total_appointments) * 100);
};

let timer;
onMounted(() => {
    updateDateTime();
    timer = setInterval(updateDateTime, 60000);
});

onUnmounted(() => {
    clearInterval(timer);
});
</script>

<style scoped>
.dashboard-wrapper {
    padding: 2rem;
    background-color: #f8fafc;
    min-height: 100vh;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header-content h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.header-content p {
    color: #64748b;
}

.date-display {
    background: white;
    padding: 0.75rem 1.25rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    color: #1e293b;
    font-weight: 500;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid transparent;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-card.primary { border-left-color: #3b82f6; }
.stat-card.success { border-left-color: #10b981; }
.stat-card.info { border-left-color: #06b6d4; }
.stat-card.warning { border-left-color: #f59e0b; }

.stat-icon {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #64748b;
}

.stat-info h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-info p {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
}

.stat-pending {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #ef4444;
    font-weight: 500;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}

.grid-section {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.section-header h2 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
}

.view-all {
    font-size: 0.875rem;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.appointment-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.75rem;
    transition: background 0.2s;
}

.appointment-item:hover {
    background: #f1f5f9;
}

.app-time {
    width: 80px;
    font-weight: 600;
    color: #3b82f6;
}

.app-details {
    flex: 1;
}

.patient-name {
    font-weight: 600;
    color: #1e293b;
}

.doctor-name {
    font-size: 0.875rem;
    color: #64748b;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.pending { background: #fef3c7; color: #d97706; }
.status-badge.confirmed { background: #dcfce7; color: #16a34a; }
.status-badge.completed { background: #e0f2fe; color: #0284c7; }
.status-badge.cancelled { background: #fee2e2; color: #dc2626; }

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #94a3b8;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.status-distribution {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.dist-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.dist-item .label {
    width: 100px;
    font-size: 0.875rem;
    color: #64748b;
}

.progress-container {
    flex: 1;
    height: 8px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 4px;
}

.progress-bar.confirmed { background: #10b981; }
.progress-bar.completed { background: #3b82f6; }
.progress-bar.cancelled { background: #ef4444; }

.dist-item .value {
    width: 30px;
    text-align: right;
    font-weight: 600;
    color: #1e293b;
}

.quick-actions h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
}

.actions-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #f1f5f9;
    border: none;
    border-radius: 0.75rem;
    color: #1e293b;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.action-btn:hover {
    background: #3b82f6;
    color: white;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>
