<template>
    <Head>
        <title>Médecins | CMC</title>
        <meta name="description" content="Gérer l'équipe médicale" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-user-md me-2"></i>
                            Équipe Médicale
                        </h4>
                        <p class="text-muted">Gérez les médecins et leurs spécialités</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher un médecin..." class="form-control" />
                            <i class="fa fa-search search-icon"></i>
                        </div>

                        <Link
                            class="btn btn-primary btn-add" :href="route('doctors.create')">
                            <i class="fa fa-plus me-1"></i>
                            <span>Ajouter un médecin</span>
                        </Link>
                    </div>
                </div>
            </div>

            
            <div class="card data-card">
                <div class="card-body">
                    <div class="table-toolbar">
                        <div class="table-info">
                            <span class="result-count">
                                <strong>{{ filteredDoctors.length }}</strong> médecin(s) trouvé(s)
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="table-header">
                                            <span>Médecin</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Spécialité</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Service</span>
                                        </div>
                                    </th>
                                    <th class="text-center">
                                        <div class="table-header justify-content-center">
                                            <span>Disponibilité</span>
                                        </div>
                                    </th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="pagedDoctors.length > 0">
                                <tr v-for="doctor in pagedDoctors" :key="doctor.id" class="user-row">
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <img :src="doctor.user.avatar_url || '/assets/img/user.jpg'"
                                                    :alt="doctor.user.firstname" />
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">
                                                    Dr. {{ doctor.user.lastname }} {{ doctor.user.firstname }}
                                                </div>
                                                <div class="user-email text-muted small">
                                                    {{ doctor.user.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <template v-if="doctor.specialties && doctor.specialties.length > 0">
                                                <span class="specialty-badge" v-for="spec in doctor.specialties.slice(0, 2)" :key="spec.id">
                                                    {{ spec.name }}
                                                </span>
                                                <span class="specialty-badge bg-secondary text-white" v-if="doctor.specialties.length > 2">
                                                    +{{ doctor.specialties.length - 2 }}
                                                </span>
                                            </template>
                                            <span v-else class="text-muted">—</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <div v-if="doctor.medical_service" class="service-badge">
                                                <i class="fa fa-stethoscope me-1"></i>
                                                {{ doctor.medical_service.name }}
                                            </div>
                                            <span v-else class="text-muted">—</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="availability-container">
                                            <label class="premium-switch" @click.stop>
                                                <input type="checkbox" :checked="doctor.is_available"
                                                    @change="toggleAvailability(doctor)">
                                                <span class="slider"></span>
                                            </label>
                                            <span class="status-text" :class="doctor.is_available ? 'text-success' : 'text-danger'">
                                                {{ doctor.is_available ? 'Disponible' : 'Indisponible' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link :href="route('doctors.show', doctor.uuid)"
                                                class="btn btn-sm btn-outline-info" title="Voir profil">
                                                <i class="fa fa-id-card"></i>
                                            </Link>
                                            <Link :href="route('doctors.edit', doctor.uuid)"
                                                class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </Link>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="Supprimer" @click="confirmDelete(doctor)">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fa fa-user-md fa-3x mb-3 text-muted"></i>
                                            <p>Aucun médecin trouvé.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="pagination-container" v-if="totalPages > 1">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li :class="['page-item', { disabled: currentPage === 1 }]">
                                    <a v-if="currentPage > 1" href="#" class="page-link" @click.prevent="goToPage(currentPage - 1)">
                                        <i class="fa fa-chevron-left"></i>
                                    </a>
                                    <span v-else class="page-link"><i class="fa fa-chevron-left"></i></span>
                                </li>

                                <li v-for="page in paginationRange" :key="page"
                                    :class="['page-item', { active: page === currentPage }]">
                                    <a v-if="page !== '...'" href="#" class="page-link" @click.prevent="goToPage(page)">
                                        {{ page }}
                                    </a>
                                    <span v-else class="page-ellipsis">...</span>
                                </li>

                                <li :class="['page-item', { disabled: currentPage === totalPages }]">
                                    <a v-if="currentPage < totalPages" href="#" class="page-link" @click.prevent="goToPage(currentPage + 1)">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                    <span v-else class="page-link"><i class="fa fa-chevron-right"></i></span>
                                </li>
                            </ul>
                        </nav>
                        <div class="pagination-info">
                            Affichage de <span class="fw-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                            à
                            <span class="fw-medium">{{ Math.min(currentPage * perPage, filteredDoctors.length) }}</span> sur
                            <span class="fw-medium">{{ filteredDoctors.length }}</span> entrées
                        </div>
                    </div>

                    <div class="pagination-info p-3 text-muted small" v-else-if="searchQuery && filteredDoctors.length < props.doctors.length">
                        {{ filteredDoctors.length }} résultat(s) sur {{ props.doctors.length }} médecins
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import Swal from 'sweetalert2';

const props = defineProps({
    doctors: Array,
});

const toast = useToast();
const searchQuery = ref('');

const filteredDoctors = computed(() => {
    if (!searchQuery.value) return props.doctors;
    const q = searchQuery.value.toLowerCase();
    return props.doctors.filter(d => {
        const fullName = `${d.user?.firstname ?? ''} ${d.user?.lastname ?? ''}`.toLowerCase();
        const specialty = d.specialties?.map(s => s.name).join(' ').toLowerCase() ?? '';
        return fullName.includes(q) || specialty.includes(q);
    });
});

const currentPage = ref(1);
const perPage = ref(10);

watch(searchQuery, () => {
    currentPage.value = 1;
});

function goToPage(pageNum) {
    currentPage.value = pageNum;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

const totalPages = computed(() => Math.ceil(filteredDoctors.value.length / perPage.value));

const pagedDoctors = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredDoctors.value.slice(start, end);
});

const paginationRange = computed(() => {
    const lastPage = totalPages.value;
    if (lastPage <= 7) {
        return Array.from({ length: lastPage }, (_, i) => i + 1);
    }

    const current = currentPage.value;
    let range = [];
    range.push(1);

    if (current <= 3) {
        range.push(2, 3, 4, 5, '...', lastPage);
    } else if (current >= lastPage - 2) {
        range.push('...', lastPage - 4, lastPage - 3, lastPage - 2, lastPage - 1, lastPage);
    } else {
        range.push('...', current - 1, current, current + 1, '...', lastPage);
    }

    return range;
});

function toggleAvailability(doctor) {
    router.patch(route('doctors.toggle-availability', doctor.uuid), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            // Le message flash du contrôleur est utilisé par défaut, 
            // mais on peut forcer un message ici si on veut
            if (page.props.flash.success) {
                toast.success(page.props.flash.success);
            }
        }
    });
}

function confirmDelete(doctor) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: `Vous allez supprimer le profil du Dr. ${doctor.user.lastname}. Cette action supprimera également son compte utilisateur.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('doctors.destroy', doctor.uuid), {
                onSuccess: () => {
                    toast.success('Médecin supprimé avec succès');
                },
                onError: () => {
                    toast.error('Erreur lors de la suppression');
                }
            });
        }
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
.page-header { margin-bottom: 2rem; .header-content { display: flex; justify-content: space-between; align-items: center; } .page-title { font-weight: 700; color: #181c32; } .header-actions { display: flex; align-items: center; gap: 1rem; } }

.search-container {
    position: relative; width: 300px;
    .form-control { padding: 0.6rem 2.5rem 0.6rem 1rem; border-radius: 8px; border: 1px solid $border-color; height: 42px; }
    .search-icon { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; pointer-events: none; }
}

.btn-add {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.5rem; border-radius: 8px; height: 42px;
    background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border: none; color: white; font-weight: 600;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.3s ease;
    &:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4); color: white; }
}

.data-card { background-color: white; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); border: none; .card-body { padding: 0; } }
.table-toolbar { padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; }

.custom-table {
    width: 100%; th { padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; font-weight: 600; color: $secondary-color; background-color: #fcfcfd; }
    td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid $border-color; }
    .user-row:hover { background-color: rgba($primary-color, 0.02); }
}

.user-info {
    display: flex; align-items: center; gap: 0.75rem;
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); img { width: 100%; height: 100%; object-fit: cover; } }
    .user-name { font-weight: 600; color: #181c32; }
}

.specialty-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; background-color: rgba($primary-color, 0.1); color: $primary-color; font-weight: 600; white-space: nowrap; }
.service-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; background-color: rgba($success-color, 0.1); color: $success-color; font-weight: 600; white-space: nowrap; }

.availability-container {
    display: flex; flex-direction: column; align-items: center; gap: 0.25rem;
    .status-text { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
}

/* Premium Switch */
.premium-switch {
    position: relative; display: inline-block; width: 46px; height: 24px;
    input { opacity: 0; width: 0; height: 0; &:checked + .slider { background-color: $success-color; } &:checked + .slider:before { transform: translateX(22px); } }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e6ef; transition: .4s; border-radius: 24px; &:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; } }
}

.action-buttons {
    display: flex; justify-content: center; gap: 0.5rem;
    .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; }
    .btn-outline-primary:hover { background-color: $primary-color; color: white; }
    .btn-outline-danger:hover { background-color: $danger-color; color: white; }
    .btn-outline-info { border-color: #0dcaf0; color: #0dcaf0; &:hover { background-color: #0dcaf0; color: white; } }
}

.pagination-container { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-top: 1px solid $border-color; .pagination-info { font-size: 0.9rem; color: #7e8299; } }
</style>
