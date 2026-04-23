<template>
    <Head>
        <title>Services Médicaux | CMC</title>
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-stethoscope me-2 text-primary"></i>
                            Services Médicaux
                        </h4>
                        <p class="text-muted">Gérez les départements et services de la clinique</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher un service..." class="form-control" />
                            <i class="fa fa-search search-icon"></i>
                        </div>
                        <Link :href="route('medical-services.create')" class="btn btn-primary btn-add">
                            <i class="fa fa-plus me-1"></i> Ajouter un service
                        </Link>
                    </div>
                </div>
            </div>

            <div class="card data-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 300px">Nom du Service</th>
                                    <th>Description</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="filteredServices.length > 0">
                                <tr v-for="service in filteredServices" :key="service.id">
                                    <td>
                                        <div class="service-info">
                                            <div class="service-icon">
                                                <i class="fa fa-hospital"></i>
                                            </div>
                                            <div class="service-name">{{ service.name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate text-muted small" style="max-width: 400px">
                                            {{ service.description || 'Pas de description' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="premium-switch-container d-inline-flex align-items-center">
                                            <label class="premium-switch">
                                                <input type="checkbox" :checked="service.is_active" @change="toggleStatus(service)">
                                                <span class="slider"></span>
                                            </label>
                                            <span class="ms-2 small fw-600" :class="service.is_active ? 'text-success' : 'text-danger'">
                                                {{ service.is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link :href="route('medical-services.show', service.id)" class="btn btn-outline-info btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                            <Link :href="route('medical-services.edit', service.id)" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </Link>
                                            <button @click="confirmDelete(service)" class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="4" class="text-center py-5">Aucun service trouvé.</td>
                                </tr>
                            </tbody>
                        </table>
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
    services: Array,
});

const toast = useToast();
const searchQuery = ref('');

const filteredServices = computed(() => {
    if (!searchQuery.value) return props.services;
    const q = searchQuery.value.toLowerCase();
    return props.services.filter(s =>
        s.name?.toLowerCase().includes(q) ||
        s.description?.toLowerCase().includes(q)
    );
});

function toggleStatus(service) {
    router.patch(route('medical-services.toggle-status', service.id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Statut mis à jour')
    });
}

function confirmDelete(service) {
    Swal.fire({
        title: 'Supprimer ?',
        text: 'Voulez-vous vraiment supprimer ce service ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('medical-services.destroy', service.id), {
                onSuccess: () => toast.success('Service supprimé')
            });
        }
    });
}
</script>

<style lang="scss" scoped>
$primary-color: #4361ee; $border-color: #e4e6ef; $body-color: #f9fafb; $border-radius: 8px;

.content-wrapper { background-color: $body-color; min-height: 100vh; padding: 2rem 0; }
.page-header { margin-bottom: 2rem; .header-content { display: flex; justify-content: space-between; align-items: center; } .header-actions { display: flex; gap: 1rem; } }

.search-container { position: relative; width: 300px; .form-control { padding-right: 2.5rem; border-radius: 8px; height: 42px; } .search-icon { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #a1a5b7; } }

.btn-add { background: $primary-color; border: none; border-radius: 8px; font-weight: 600; padding: 0 1.5rem; display: flex; align-items: center; height: 42px; color: white; }

.data-card { border: none; border-radius: 12px; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05); }

.custom-table {
    th { padding: 1.25rem 1.5rem; border-bottom: 1px solid $border-color; font-weight: 600; background: #fcfcfd; }
    td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid $border-color; }
}

.service-info { display: flex; align-items: center; gap: 0.75rem; .service-icon { width: 35px; height: 35px; background: rgba($primary-color, 0.1); color: $primary-color; border-radius: 8px; display: flex; align-items: center; justify-content: center; } .service-name { font-weight: 600; } }

.action-buttons { display: flex; justify-content: center; gap: 0.5rem; .btn { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; } }

.premium-switch {
    position: relative; display: inline-block; width: 40px; height: 20px;
    input { opacity: 0; width: 0; height: 0; &:checked + .slider { background-color: #0abb87; } &:checked + .slider:before { transform: translateX(20px); } }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e6ef; transition: .4s; border-radius: 20px; &:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; } }
}

.fw-600 { font-weight: 600; }
</style>
