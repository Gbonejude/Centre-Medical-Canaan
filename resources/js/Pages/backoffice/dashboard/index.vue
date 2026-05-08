<template>
    <Head>
        <title>Dashboard Hospitalier | CCS</title>
        <meta name="description" content="Tableau de bord de gestion hospitalière" />
    </Head>

    <div class="dashboard-wrapper">
        <!-- Header / Welcome Banner -->
        <div class="welcome-banner">
            <div class="banner-content">
                <div class="banner-text">
                    <div class="date-time-badge mb-3">
                        <i class="far fa-clock me-2"></i> {{ currentDateTime }}
                    </div>
                    <h1>Bonjour, {{ $page.props.auth.user.firstname }} ! </h1>
                    <p>Voici un aperçu de l'activité du Centre Médical pour aujourd'hui.</p>
                </div>
                <div class="banner-stats">
                    <div class="mini-stat">
                        <span class="value">{{ props.stats.pending_appointments }}</span>
                        <span class="label">À traiter</span>
                    </div>
                    <div class="mini-stat">
                        <span class="value">{{ props.todayAppointments.length }}</span>
                        <span class="label">RDV aujourd'hui</span>
                    </div>
                </div>
            </div>
            <!-- Subtle background pattern/circles -->
            <div class="banner-pattern"></div>
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
                    <h2>{{ props.stats.total_patients }}</h2>
                    <p>PATIENTS ENREGISTRÉS</p>
                </div>
            </div>

            <div class="stat-card info" @click="$inertia.visit(route('doctors.index'))">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-info">
                    <h2>{{ props.stats.total_doctors }}</h2>
                    <p>MÉDECINS</p>
                </div>
            </div>

            <!-- Nouvelle carte pour Services (Optionnel si tu veux équilibrer) -->
              <div class="stat-card warning" @click="$inertia.visit(route('medical-services.index'))">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-info">
                    <h2>{{ props.stats.total_services }}</h2>
                    <p>SERVICES ACTIFS</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="dashboard-grid">
            
            <!-- Left Column: Trends & Main Charts -->
            <div class="grid-section">
                <!-- Graphique Tendance -->
                <div class="chart-container main-chart">
                    <div class="section-header">
                        <h2><i class="fas fa-chart-line me-2"></i>Évolution hebdomadaire des RDV</h2>
                    </div>
                    <div class="chart-wrapper">
                        <LineChart :data="chartDataTrends" :options="chartOptionsLine" />
                    </div>
                </div>

                <!-- Hourly Activity -->
                <div class="chart-container mt-4">
                    <div class="section-header">
                        <h2><i class="fas fa-clock me-2"></i>Volume horaire des consultations</h2>
                    </div>
                    <div class="chart-wrapper">
                        <BarChart :data="chartDataHourly" :options="chartOptionsHourly" />
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="section-header mt-5">
                    <h2><i class="fas fa-calendar-day me-2"></i>Rendez-vous du Jour</h2>
                    <Link :href="route('appointments.index')" class="view-all">Voir tout</Link>
                </div>
                <div class="appointments-list">
                    <div v-if="props.todayAppointments.length === 0" class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <p>Aucun rendez-vous prévu pour aujourd'hui.</p>
                    </div>
                    <div v-else v-for="app in props.todayAppointments" :key="app.id" class="appointment-item">
                        <div class="app-time">{{ app.time }}</div>
                        <div class="app-details">
                            <div class="patient-name">{{ app.patient }}</div>
                            <div class="doctor-name">{{ app.doctor }} • {{ app.service }}</div>
                        </div>
                        <div class="app-status d-flex align-items-center gap-2">
                            <span :class="'status-badge ' + app.status_key">
                                {{ app.status }}
                            </span>
                            <button v-if="app.status_key === 'confirmed'" 
                                @click="markNoShow(app)"
                                class="btn-noshow-mini" 
                                title="Marquer absent">
                                <i class="fas fa-user-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Distribution Charts -->
            <div class="grid-section sidebar-section">
                
                <!-- Status Distribution (Donut) -->
                <div class="chart-container-small mb-4">
                    <div class="section-header">
                        <h2><i class="fas fa-tasks me-2"></i>État des Rendez-vous (Global)</h2>
                    </div>
                    <div class="chart-wrapper-small">
                        <DoughnutChart :data="chartDataStatuses" :options="chartOptionsDoughnut" />
                    </div>
                </div>

                <!-- Répartition par Service -->
                <div class="chart-container-small mb-4">
                    <div class="section-header">
                        <h2><i class="fas fa-hospital me-2"></i>Demande par Service Médical</h2>
                    </div>
                    <div class="chart-wrapper-small">
                        <PieChart :data="chartDataServices" :options="chartOptionsDoughnut" />
                    </div>
                </div>

                <!-- Top Médecins -->
                <div class="chart-container-small mb-4">
                    <div class="section-header">
                        <h2><i class="fas fa-user-md me-2"></i>Médecins les plus sollicités</h2>
                    </div>
                    <div class="chart-wrapper-small">
                        <BarChart :data="chartDataDoctors" :options="chartOptionsBar" />
                    </div>
                </div>

                <div class="quick-actions">
                    <h3>Actions Rapides</h3>
                    <div class="actions-grid">
                        <button @click="$inertia.visit(route('medical-services.index'))" class="action-btn">
                            <i class="fas fa-hospital-symbol"></i> Services
                        </button>
                        <button @click="$inertia.visit(route('schedules.index'))" class="action-btn">
                            <i class="fas fa-calendar-alt"></i> Plannings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import { useToast } from "vue-toastification";

// Import des composants Chart.js
import { Line, Doughnut, Bar, Pie } from 'vue-chartjs';
import { 
    Chart as ChartJS, 
    Title, 
    Tooltip, 
    Legend, 
    LineElement, 
    PointElement, 
    CategoryScale, 
    LinearScale,
    ArcElement,
    BarElement
} from 'chart.js';

// Enregistrer les modules ChartJS
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, ArcElement, BarElement);

// Créer des composants locaux
const LineChart = Line; 
const DoughnutChart = Doughnut; 
const PieChart = Pie;
const BarChart = Bar; 

const toast = useToast();

const props = defineProps({
    stats: Object,
    charts: {
        type: Object,
        default: () => ({
            trends: [],
            services: [],
            top_doctors: [],
            statuses: [],
            hourly: []
        })
    },
    todayAppointments: Array,
});

function markNoShow(app) {
    Swal.fire({
        title: 'Patient absent ?',
        text: 'Veuillez indiquer le motif de l\'absence du patient :',
        input: 'textarea',
        inputPlaceholder: 'Motif...',
        showCancelButton: true,
        confirmButtonText: 'Confirmer l\'absence',
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Annuler',
        inputValidator: (value) => {
            if (!value) return 'Le motif est obligatoire !'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route('appointments.update-status', app.uuid || app.id), { 
                status: 'NO_SHOW', 
                notes: result.value 
            }, {
                onSuccess: () => toast.success('Patient marqué comme absent')
            });
        }
    });
}

// --- Formatage des Données pour les Graphiques ---

// 1. Graphique Ligne (Tendance)
const chartDataTrends = computed(() => {
    return {
        labels: props.charts.trends.map(item => {
            // Formater la date ex: 2023-10-25 -> 25 Oct
            const date = new Date(item.date);
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        }),
        datasets: [
            {
                label: 'RDV',
                backgroundColor: '#3b82f6',
                borderColor: '#3b82f6',
                data: props.charts.trends.map(item => item.count),
                tension: 0.4, // Courbe lisse
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.1)'
            }
        ]
    };
});

const chartOptionsLine = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
        x: { grid: { display: false } }
    }
};

// 2. Graphique Donut (Services)
const chartDataServices = computed(() => {
    return {
        labels: props.charts.services.map(item => item.label),
        datasets: [
            {
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'],
                data: props.charts.services.map(item => item.count)
            }
        ]
    };
});

// 3. Graphique Barres (Médecins)
const chartDataDoctors = computed(() => {
    return {
        labels: props.charts.top_doctors.map(item => item.label),
        datasets: [
            {
                label: 'RDV',
                backgroundColor: '#6366f1',
                borderRadius: 4,
                data: props.charts.top_doctors.map(item => item.count)
            }
        ]
    };
});

// 4. Graphique Statuts (Donut)
const chartDataStatuses = computed(() => {
    const colors = {
        pending: '#f59e0b',
        confirmed: '#10b981',
        completed: '#3b82f6',
        cancelled: '#ef4444',
        no_show: '#6b7280'
    };
    return {
        labels: props.charts.statuses.map(item => item.label),
        datasets: [
            {
                backgroundColor: props.charts.statuses.map(item => colors[item.status_key] || '#94a3b8'),
                data: props.charts.statuses.map(item => item.count)
            }
        ]
    };
});

// 5. Activité Horaire (Bar)
const chartDataHourly = computed(() => {
    return {
        labels: props.charts.hourly.map(item => item.hour),
        datasets: [
            {
                label: 'Volume de RDV',
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 5,
                data: props.charts.hourly.map(item => item.count)
            }
        ]
    };
});

const chartOptionsDoughnut = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { 
        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } 
    },
    cutout: '70%',
};

const chartOptionsBar = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { beginAtZero: true, grid: { display: false } },
        y: { grid: { display: false } }
    }
};

const chartOptionsHourly = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
        x: { grid: { display: false } }
    }
};

// --- Autres logiques (Date, etc) ---
const currentDateTime = ref("");

const updateDateTime = () => {
    const now = new Date();
    currentDateTime.value = now.toLocaleDateString('fr-FR', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    }) + " " + now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
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
.welcome-banner {
    background: #0d6efdab; /* Deep Navy/Slate */
    border-radius: 1.5rem;
    padding: 2rem 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.banner-pattern {
    position: absolute;
    top: 0;
    right: 0;
    width: 40%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03));
    mask-image: radial-gradient(circle at center, black 1px, transparent 1px);
    mask-size: 20px 20px;
    z-index: 1;
}

.date-time-badge {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.4rem 1rem;
    border-radius: 99px;
    font-size: 0.85rem;
    display: inline-block;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    font-weight: 500;
}

.banner-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.banner-text h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: white;
}

.banner-text p {
    font-size: 1rem;
    opacity: 0.8;
}

.banner-stats {
    display: flex;
    gap: 1.5rem;
}

.mini-stat {
    text-align: center;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.8rem 1.25rem;
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    min-width: 120px;
}

.mini-stat .value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.mini-stat .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    font-weight: 600;
    opacity: 0.7;
    letter-spacing: 0.5px;
}

.dashboard-wrapper {
    padding: 2rem;
    background-color: #f8fafc;
    min-height: 100vh;
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
    display: flex;
    align-items: center;
}

.chart-wrapper {
    height: 300px;
    position: relative;
}

.chart-wrapper-small {
    height: 220px;
    position: relative;
}

.main-chart {
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 2rem;
}

.sidebar-section {
    background-color: transparent !important;
    box-shadow: none !important;
    padding: 0 !important;
}

.sidebar-section .chart-container-small {
    background: white;
    padding: 1.25rem;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
.status-badge.no_show { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

.btn-noshow-mini {
    background: none;
    border: 1px solid #fca5a5;
    color: #ef4444;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-noshow-mini:hover {
    background: #ef4444;
    color: white;
}

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
