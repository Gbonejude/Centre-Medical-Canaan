<template>
    <Head>
        <title>Planning des Médecins | CMC</title>
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-calendar-alt me-2"></i>
                            Planning & Disponibilités
                        </h4>
                        <p class="text-muted">Configurez les horaires de travail de l'équipe médicale</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher un médecin..." class="form-control"
                                @input="handleSearch" />
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" v-if="doctors.data.length > 0">
                <div v-for="doctor in doctors.data" :key="doctor.id" class="col-md-6 col-lg-4 mb-4">
                    <div class="card doctor-schedule-card h-100">
                        <div class="card-body">
                            <div class="doctor-profile">
                                <div class="avatar">
                                    <img :src="doctor.user.avatar_url || '/assets/img/user.jpg'" alt="Doctor" />
                                </div>
                                <div class="info">
                                    <div class="name">Dr. {{ doctor.user.lastname }} {{ doctor.user.firstname }}</div>
                                    <div class="specialty text-muted small" v-if="doctor.specialty">{{ doctor.specialty.name }}</div>
                                    <div class="specialty text-muted small" v-else>{{ doctor.specialty_title || 'Généraliste' }}</div>
                                </div>
                            </div>
                            
                            <div class="schedule-summary mt-4">
                                <div class="summary-title">Aperçu de la semaine</div>
                                <div class="days-strip">
                                    <span v-for="(day, key) in dayLabels" :key="key" 
                                          class="day-dot" 
                                          :class="{ 'active': isDayEnabled(doctor, key) }"
                                          :title="day">
                                        {{ day.charAt(0) }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-actions mt-4">
                                <Link :href="route('schedules.edit', doctor.id)" class="btn btn-primary w-100">
                                    <i class="fa fa-clock me-2"></i> Gérer les horaires
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-5">
                <div class="empty-state">
                    <i class="fa fa-calendar-times fa-3x mb-3 text-muted"></i>
                    <p class="text-muted">Aucun médecin trouvé pour cette recherche.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination-container mt-4" v-if="doctors.links && doctors.links.length > 3">
                <div class="pagination-info">
                    Affichage de {{ doctors.from }} à {{ doctors.to }} sur {{ doctors.total }} médecins
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li v-for="(link, index) in doctors.links" :key="index" class="page-item"
                            :class="{ 'active': link.active, 'disabled': !link.url }">
                            <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const props = defineProps({
    doctors: Object,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');

const handleSearch = debounce(() => {
    router.get(route('schedules.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true
    });
}, 500);

const dayLabels = {
    monday: 'Lundi',
    tuesday: 'Mardi',
    wednesday: 'Mercredi',
    thursday: 'Jeudi',
    friday: 'Vendredi',
    saturday: 'Samedi',
    sunday: 'Dimanche',
};

function isDayEnabled(doctor, dayKey) {
    if (!doctor.availability || !doctor.availability[dayKey]) return false;
    return doctor.availability[dayKey].enabled;
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee;
$secondary-color: #3f4254;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$border-radius: 0.75rem;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.page-header { margin-bottom: 2rem; .header-content { display: flex; justify-content: space-between; align-items: center; } .page-title { font-weight: 700; color: #181c32; } }

.search-container {
    position: relative; width: 300px;
    .form-control { padding: 0.6rem 2.5rem 0.6rem 1rem; border-radius: 8px; border: 1px solid $border-color; height: 42px; }
    .search-icon { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; pointer-events: none; }
}

.doctor-schedule-card {
    border: none; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); transition: transform 0.3s ease;
    &:hover { transform: translateY(-5px); }
    .doctor-profile {
        display: flex; align-items: center; gap: 1rem;
        .avatar { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; img { width: 100%; height: 100%; object-fit: cover; } }
        .name { font-weight: 700; color: #181c32; }
    }
}

.schedule-summary {
    .summary-title { font-size: 0.8rem; color: #7e8299; text-transform: uppercase; margin-bottom: 0.75rem; font-weight: 600; }
    .days-strip {
        display: flex; gap: 0.5rem;
        .day-dot {
            width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; background-color: #f1f3f9; color: #a1a5b7;
            &.active { background-color: rgba($primary-color, 0.1); color: $primary-color; }
        }
    }
}

.btn-primary { background: $primary-color; border: none; border-radius: 8px; padding: 0.6rem; font-weight: 600; }

.pagination-container { display: flex; justify-content: space-between; align-items: center; .pagination-info { font-size: 0.9rem; color: #7e8299; } }
</style>
