<template>
    <Head>
        <title>Spécialités | CMC</title>
        <meta name="description" content="Gérer les spécialités médicales" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-award me-2"></i>
                            Spécialités Médicales
                        </h4>
                        <p class="text-muted">Gérez les titres et spécialités de votre équipe médicale</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher une spécialité..." class="form-control"
                                @input="handleSearch" />
                            <i class="fa fa-search search-icon"></i>
                        </div>
                        <Link :href="route('specialties.create')" class="btn btn-primary add-btn">
                            <i class="fa fa-plus-circle me-1"></i>
                            Nouvelle Spécialité
                        </Link>
                    </div>
                </div>
            </div>

            
            <div class="card data-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Nom de la Spécialité</th>
                                    <th>Description</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="specialties.data.length > 0">
                                <tr v-for="specialty in specialties.data" :key="specialty.id" class="data-row">
                                    <td>
                                        <div class="specialty-info">
                                            <div class="icon-box">
                                                <i class="fa fa-stethoscope"></i>
                                            </div>
                                            <div class="name-container">
                                                <div class="specialty-name">{{ specialty.name }}</div>
                                                <div class="specialty-slug text-muted small">{{ specialty.slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="description-cell">
                                        <div class="text-truncate-2" :title="specialty.description">
                                            {{ specialty.description || '—' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <label class="premium-switch" @click.stop>
                                            <input type="checkbox" :checked="specialty.is_active"
                                                @change="toggleStatus(specialty)">
                                            <span class="slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link :href="route('specialties.edit', specialty.uuid)"
                                                class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </Link>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="Supprimer" @click="confirmDelete(specialty)">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fa fa-award fa-3x mb-3 text-muted"></i>
                                            <p>Aucune spécialité trouvée.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="pagination-container" v-if="specialties.links && specialties.links.length > 3">
                        <div class="pagination-info">
                            Affichage de {{ specialties.from }} à {{ specialties.to }} sur {{ specialties.total }} spécialités
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li v-for="(link, index) in specialties.links" :key="index" class="page-item"
                                    :class="{ 'active': link.active, 'disabled': !link.url }">
                                    <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import Swal from 'sweetalert2';
import debounce from 'lodash/debounce';

const props = defineProps({
    specialties: Object,
    filters: Object,
});

const toast = useToast();
const searchQuery = ref(props.filters.search || '');

const handleSearch = debounce(() => {
    router.get(route('specialties.index'), { search: searchQuery.value }, {
        preserveState: true,
        replace: true
    });
}, 500);

function toggleStatus(specialty) {
    router.patch(route('specialties.toggle-status', specialty.uuid), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Statut mis à jour');
        }
    });
}

function confirmDelete(specialty) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: `Voulez-vous vraiment supprimer la spécialité "${specialty.name}" ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('specialties.destroy', specialty.uuid), {
                onSuccess: () => {
                    toast.success('Spécialité supprimée avec succès');
                },
                onError: (errors) => {
                    if (errors.error) toast.error(errors.error);
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
$border-radius: 8px;

.content-wrapper { background-color: $body-color; min-height: calc(100vh - 65px); padding: 2rem 0; }
.page-header { margin-bottom: 2rem; .header-content { display: flex; justify-content: space-between; align-items: center; } .page-title { font-weight: 700; color: #181c32; margin: 0; } .header-actions { display: flex; align-items: center; gap: 1rem; } }

.search-container { position: relative; width: 300px; .form-control { padding: 0.6rem 2.5rem 0.6rem 1rem; border-radius: 8px; border: 1px solid $border-color; height: 42px; } .search-icon { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; pointer-events: none; } }

.add-btn { background: $primary-color; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 10px rgba($primary-color, 0.2); transition: all 0.3s ease; &:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba($primary-color, 0.3); } }

.data-card { background-color: white; border-radius: $border-radius; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); border: none; overflow: hidden; .card-body { padding: 0; } }

.custom-table {
    width: 100%; th { padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; font-weight: 600; color: $secondary-color; background-color: #fcfcfd; }
    td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid $border-color; }
    .data-row:hover { background-color: rgba($primary-color, 0.02); }
}

.specialty-info {
    display: flex; align-items: center; gap: 1rem;
    .icon-box { width: 40px; height: 40px; border-radius: 10px; background: rgba($primary-color, 0.1); color: $primary-color; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .specialty-name { font-weight: 600; color: #181c32; }
}

.description-cell { max-width: 300px; .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #7e8299; font-size: 0.9rem; } }

/* Premium Switch */
.premium-switch {
    position: relative; display: inline-block; width: 46px; height: 24px;
    input { opacity: 0; width: 0; height: 0; &:checked + .slider { background-color: $success-color; } &:checked + .slider:before { transform: translateX(22px); } }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e6ef; transition: .4s; border-radius: 24px; &:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; } }
}

.action-buttons {
    display: flex; justify-content: center; gap: 0.5rem;
    .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; i { font-size: 0.9rem; } }
}

.pagination-container { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; .pagination-info { font-size: 0.9rem; color: #7e8299; } }
</style>
