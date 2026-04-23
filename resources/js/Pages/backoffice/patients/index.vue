<template>
    <Head>
        <title>Patients | CMC</title>
        <meta name="description" content="Gérer les dossiers patients" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-users me-2"></i>
                            Dossiers Patients
                        </h4>
                        <p class="text-muted">Liste des patients enregistrés dans le système</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher un patient..." class="form-control" />
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card data-card">
                <div class="card-body">
                    <div class="table-toolbar">
                        <div class="table-info">
                            <span class="result-count">
                                <strong>{{ filteredPatients.length }}</strong> patient(s) trouvé(s)
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="table-header">
                                            <span>Patient</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Âge / Date de Naissance</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Genre</span>
                                        </div>
                                    </th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="filteredPatients.length > 0">
                                <tr v-for="patient in filteredPatients" :key="patient.id" class="user-row">
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <img :src="patient.user.avatar_url || '/assets/img/user.jpg'"
                                                    :alt="patient.user.firstname" />
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">
                                                    {{ patient.user.lastname }} {{ patient.user.firstname }}
                                                </div>
                                                <div class="user-email text-muted small">
                                                    {{ patient.user.phone || patient.user.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dob-info">
                                            <span class="age-value">{{ calculateAge(patient.date_of_birth) }} ans</span>
                                            <div class="text-muted small">{{ formatDate(patient.date_of_birth) }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="gender-badge" :class="patient.gender?.toLowerCase()">
                                            {{ translateGender(patient.gender) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link :href="route('patients.show', patient.id)"
                                                class="btn btn-sm btn-outline-info" title="Voir dossier">
                                                <i class="fa fa-file-medical"></i>
                                            </Link>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="Supprimer" @click="confirmDelete(patient)">
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
                                            <i class="fa fa-users fa-3x mb-3 text-muted"></i>
                                            <p>Aucun patient trouvé.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="pagination-info p-3 text-muted small" v-if="searchQuery && filteredPatients.length < props.patients.length">
                        {{ filteredPatients.length }} résultat(s) sur {{ props.patients.length }} patients
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import Swal from 'sweetalert2';

const props = defineProps({
    patients: Array,
});

const toast = useToast();
const searchQuery = ref('');

const filteredPatients = computed(() => {
    if (!searchQuery.value) return props.patients;
    const q = searchQuery.value.toLowerCase();
    return props.patients.filter(p => {
        const fullName = `${p.user?.firstname ?? ''} ${p.user?.lastname ?? ''}`.toLowerCase();
        const email = (p.user?.email ?? '').toLowerCase();
        const phone = (p.user?.phone ?? '').toLowerCase();
        return fullName.includes(q) || email.includes(q) || phone.includes(q);
    });
});

function formatDate(date) {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('fr-FR');
}

function calculateAge(dob) {
    if (!dob) return '—';
    const diff = Date.now() - new Date(dob).getTime();
    return Math.abs(new Date(diff).getUTCFullYear() - 1970);
}

function translateGender(gender) {
    if (!gender) return '—';
    const g = gender.toLowerCase();
    if (g === 'male' || g === 'masculin') return 'Masculin';
    if (g === 'female' || g === 'féminin') return 'Féminin';
    return gender;
}

function confirmDelete(patient) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: `Vous allez supprimer le dossier de ${patient.user.lastname}. Cette action est irréversible.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('patients.destroy', patient.id), {
                onSuccess: () => {
                    toast.success('Dossier patient supprimé');
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

.gender-badge {
    padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;
    &.masculin, &.male { background-color: rgba($primary-color, 0.1); color: $primary-color; }
    &.féminin, &.female { background-color: rgba(#e83e8c, 0.1); color: #e83e8c; }
}

.blood-badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; background-color: rgba($danger-color, 0.1); color: $danger-color; border: 1px solid rgba($danger-color, 0.2); }

.action-buttons {
    display: flex; justify-content: center; gap: 0.5rem;
    .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; }
    .btn-outline-danger:hover { background-color: $danger-color; color: white; }
    .btn-outline-info { border-color: #0dcaf0; color: #0dcaf0; &:hover { background-color: #0dcaf0; color: white; } }
}

.pagination-container { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-top: 1px solid $border-color; .pagination-info { font-size: 0.9rem; color: #7e8299; } }
</style>
