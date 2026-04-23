<template>

    <Head>
        <title>Gestion des utilisateurs</title>
        <meta name="description" content="Gérer les utilisateurs du système et leurs permissions" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="fa fa-users me-2"></i>
                            Gestion des utilisateurs
                        </h4>
                        <p class="text-muted">Gérer les comptes utilisateurs et les permissions de votre système</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher des utilisateurs..." class="form-control"
                                @input="handleSearch" />
                            <i class="fa fa-search search-icon"></i>
                        </div>

                        <div class="permission-multiselect" ref="dropdownRef">
                            <button type="button" class="permission-trigger" @click="dropdownOpen = !dropdownOpen">
                                <span>{{ selectedPermissionsLabel }}</span>
                                <i class="fa fa-chevron-down ms-auto"></i>
                            </button>
                            <div v-if="dropdownOpen" class="permission-dropdown">
                                <label class="permission-option">
                                    <input type="checkbox" :checked="permissionFilters.length === 0" @change="selectAllPermissions" />
                                    <span>Toutes les permissions</span>
                                </label>
                                <label v-for="p in staffPermissions" :key="p.id" class="permission-option">
                                    <input type="checkbox" :value="p.encoded_id" v-model="permissionFilters" @change="handleFilterChange" />
                                    <span>{{ p.name }}</span>
                                </label>
                            </div>
                        </div>

                        <Link
                            v-if="userHasPermission([PERMISSIONS.PROGRAM_DIRECTOR, PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.OFFICE])"
                            class="btn btn-primary btn-add" :href="route('users.create')">
                        <i class="fa fa-user-plus me-1"></i>
                        <span>Ajouter un utilisateur</span>
                        </Link>
                    </div>
                </div>
            </div>

            
            <div class="card data-card">
                <div class="card-body">
                    <div class="table-toolbar">
                        <div class="table-info">
                            <span class="result-count">
                                <strong>{{ allFilteredUsers.length }}</strong> {{ allFilteredUsers.length === 1 ? 'utilisateur trouvé' : 'utilisateurs trouvés' }}
                            </span>
                            <span v-if="permissionFilters.length > 0" class="active-filter-badge">
                                <i class="fa fa-filter me-1"></i>
                                {{ selectedPermissionsLabel }}
                                <button @click="clearFilters" class="btn-clear-inline">
                                    <i class="fa fa-times"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="table-header">
                                            <span>Utilisateur</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span>E-mail</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Téléphone</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Rôle</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span>Genre</span>
                                        </div>
                                    </th>
                                    <th
                                        v-if="userHasPermission([PERMISSIONS.PROGRAM_DIRECTOR, PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.OFFICE])">
                                        <div class="table-header">
                                            <span>Statut</span>
                                        </div>
                                    </th>
                                    <th v-if="userHasPermission([PERMISSIONS.PROGRAM_DIRECTOR, PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.OFFICE])"
                                        class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="pagedUsers.length > 0">
                                <tr v-for="user in pagedUsers" :key="user.uuid" class="user-row">
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <img :src="user.avatar_url || '/assets/img/user.jpg'"
                                                    :alt="formatUserName(user)"
                                                    @error="handleImageError" />

                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">
                                                    {{ formatUserName(user) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <a :href="`mailto:${user.email}`" class="user-email">
                                            <i class="fa fa-envelope email-icon"></i>
                                            {{ user.email }}
                                        </a>
                                    </td>
                                    <td>
                                        <div v-if="user.phone" class="user-phone">
                                            <i class="fa fa-phone-alt phone-icon"></i>
                                            <a :href="`tel:${user.phone}`">{{ user.phone }}</a>
                                        </div>
                                        <div v-else class="text-muted">Non fourni</div>
                                    </td>
                                    <td>
                                        <div class="user-permissions-cell">
                                            <template v-if="user.display_permissions && user.display_permissions.length > 0">
                                                <span v-for="perm in user.display_permissions.slice(0, 2)" :key="perm.id" class="role-badge">
                                                    {{ translateRole(perm.name) }}
                                                </span>
                                                <span v-if="user.display_permissions.length > 2" class="role-badge role-badge-more">
                                                    +{{ user.display_permissions.length - 2 }}
                                                </span>
                                            </template>
                                            <span v-else class="text-muted">—</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="gender-badge" :class="user.gender">
                                            <i :class="[
                                                'gender-icon',
                                                user.gender === 'male' ? 'fa fa-mars' : 'fa fa-venus'
                                            ]"></i>
                                            <span>{{ user.gender === 'male' ? 'Homme' : 'Femme' }}</span>
                                        </div>
                                    </td>
                                    <td v-if="userHasPermission([PERMISSIONS.PROGRAM_DIRECTOR, PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.OFFICE])"
                                        class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" :id="`status-${user.uuid}`"
                                                :checked="user.active" @change="toggleUserStatus(user)"
                                                :disabled="loadingStatus === user.uuid" style="cursor: pointer;">
                                            <label class="form-check-label visually-hidden"
                                                :for="`status-${user.uuid}`">
                                                Toggle Status
                                            </label>
                                        </div>
                                        <small class="d-block mt-1"
                                            :class="user.active ? 'text-success' : 'text-danger'">
                                            {{ user.active ? 'Actif' : 'Inactif' }}
                                        </small>
                                    </td>
                                    <td
                                        v-if="userHasPermission([PERMISSIONS.PROGRAM_DIRECTOR, PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.OFFICE])">
                                        <div class="action-buttons">
                                            <Link :href="buildUserNavigationUrl('users.show', user.uuid)"
                                                class="btn btn-sm btn-outline-info" title="Voir le profil">
                                            <i class="fa fa-id-card"></i>
                                            </Link>
                                            <Link :href="buildUserNavigationUrl('users.edit', user.uuid)"
                                                class="btn btn-sm btn-outline-primary" title="Modifier l'utilisateur">
                                            <i class="fa fa-edit"></i>
                                            </Link>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="Supprimer l'utilisateur" @click="setSelectedUserId(user.uuid)">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fa fa-user-slash fa-3x text-muted"></i>
                                            <p class="mt-2">Aucun utilisateur trouvé correspondant à vos critères.</p>
                                            <Link class="btn btn-sm btn-outline-primary mt-2"
                                                :href="route('users.create')">
                                            <i class="fa fa-user-plus me-1"></i> Ajoutez votre premier utilisateur
                                            </Link>
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
                            <span class="fw-medium">{{ Math.min(currentPage * perPage, allFilteredUsers.length)
                            }}</span> sur
                            <span class="fw-medium">{{ allFilteredUsers.length }}</span> entrées
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div v-if="showActionToast" class="action-toast">
        <div class="action-toast-content">
            <i class="fa fa-check-circle me-2"></i>
            <span>{{ actionToastMessage }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useForm, Link, usePage, router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import { userHasPermission } from "../../../utils/auth.js";
import { PERMISSIONS } from "../../../constants/permission.js";
const toast = useToast();
const page = usePage();
const showActionToast = ref(false);
const actionToastMessage = ref("");

const props = defineProps({
    users: Array,
    filters: Object,
    staffPermissions: Array,
    defaultRate: {
        type: Number,
        default: 14
    }
});

const searchQuery = ref(props.filters?.search || "");
// Support multiple permission filters - parse comma-separated encoded IDs
const permissionFilters = ref(
    props.filters?.permission
        ? props.filters.permission.split(',').filter(Boolean)
        : []
);
const selectedUserId = ref(null);
const loadingStatus = ref(null);
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

const currentPage = ref(1);
const perPage = ref(10);

const selectedPermissionsLabel = computed(() => {
    if (permissionFilters.value.length === 0) return 'Toutes les permissions';
    if (permissionFilters.value.length === 1) {
        const found = props.staffPermissions?.find(p => p.encoded_id === permissionFilters.value[0]);
        return found ? translateRole(found.name) : '1 permission sélectionnée';
    }
    return `${permissionFilters.value.length} permissions sélectionnées`;
});

function translateRole(roleName) {
    if (!roleName) return '';
    const roles = {
        'ADMIN': 'Administrateur',
        'RECEPTIONIST': 'Réceptionniste',
        'DOCTOR': 'Médecin',
        'PATIENT': 'Patient',
        'SUPER ADMIN': 'Super Admin',
        'OFFICE': 'Bureau',
        'CAREGIVER': 'Soignant',
        'PROGRAM DIRECTOR': 'Directeur de Programme'
    };
    return roles[roleName.toUpperCase()] || roleName;
}

function selectAllPermissions() {
    permissionFilters.value = [];
    handleFilterChange();
}

function handleClickOutside(event) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        dropdownOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

function buildFilterParams() {
    const params = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (permissionFilters.value.length > 0) params.p = permissionFilters.value.join(',');
    return params;
}

function buildUserNavigationUrl(routeName, userUuid) {
    const baseUrl = route(routeName, userUuid);
    const query = new URLSearchParams(buildFilterParams());
    const queryString = query.toString();
    return queryString ? `${baseUrl}?${queryString}` : baseUrl;
}

function goToPage(pageNum) {
    currentPage.value = pageNum;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleSearch() {
    currentPage.value = 1;
}

function handleFilterChange() {
    dropdownOpen.value = false;
    currentPage.value = 1;
}

function clearFilters() {
    searchQuery.value = "";
    permissionFilters.value = [];
    currentPage.value = 1;
}

function formatUserName(user) {
    const lastname = user?.lastname?.trim?.() || "";
    const firstname = user?.firstname?.trim?.() || "";

    if (lastname && firstname) {
        return `${lastname}, ${firstname}`;
    }

    return lastname || firstname || "";
}

const allFilteredUsers = computed(() => {
    let result = props.users ? [...props.users] : [];

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (user) =>
                (user.lastname && user.lastname.toLowerCase().includes(query)) ||
                (user.firstname && user.firstname.toLowerCase().includes(query)) ||
                (user.email && user.email.toLowerCase().includes(query)) ||
                (user.phone && user.phone.includes(query)) ||
                (user.username && user.username.toLowerCase().includes(query))
        );
    }

    // Filter by permissions
    if (permissionFilters.value.length > 0) {
        result = result.filter(user => {
            if (!user.display_permissions) return false;
            return user.display_permissions.some(up => 
                permissionFilters.value.includes(up.encoded_id)
            );
        });
    }

    // Sort alphabetically by Lastname, then Firstname
    result.sort((a, b) => {
        const nameA = ((a.lastname || '') + (a.firstname || '')).toLowerCase();
        const nameB = ((b.lastname || '') + (b.firstname || '')).toLowerCase();
        return nameA.localeCompare(nameB);
    });

    return result;
});

const totalPages = computed(() => Math.ceil(allFilteredUsers.value.length / perPage.value));

const pagedUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return allFilteredUsers.value.slice(start, end);
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

function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

const setSelectedUserId = (uuid) => {
    selectedUserId.value = uuid;

    
    const user = allFilteredUsers.value.find(u => u.uuid === uuid);
    const userName = user ? `${user.firstname} ${user.lastname}` : 'cet utilisateur';

    Swal.fire({
        title: "Confirmer la suppression de l'utilisateur",
        text: `Êtes-vous sûr de vouloir supprimer ${userName} ? Cette action est irréversible.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimer l'utilisateur",
        cancelButtonText: "Annuler",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) deleteUser();
    });
};

const formatCurrency = (amount) => {
    return Number(amount).toFixed(2);
};
const toggleUserStatus = (user) => {
    
    if (loadingStatus.value === user.uuid) return;

    loadingStatus.value = user.uuid;

    
    const statusForm = useForm({});
    const handleImageError = (event) => {
        event.target.src = '/assets/img/user.jpg';
    };

    statusForm.patch(route("users.updateStatus", user.uuid), {
        onSuccess: () => {
            loadingStatus.value = null;
            console.log(props)
            if (page.props.flash.success) {
                toast.success(page.props.flash.success, {
                    position: "top-right",
                    timeout: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                });
            }
        },
        onError: () => {
            loadingStatus.value = null;
            if (page.props.flash.error) {
                toast.error(page.props.flash.error, {
                    position: "top-right",
                    timeout: 5000,
                    closeOnClick: true,
                    pauseOnHover: true,
                });
            }
        },
        onFinish: () => {
            loadingStatus.value = null;
        }
    });
};
function deleteUser() {
    const userUuid = selectedUserId.value;
    if (!userUuid) return;

    useForm({}).delete(route("users.destroy", userUuid), {
        onSuccess: (page) => {
            if (page.props?.flash?.success) {
                toast.success(page.props.flash.success, {
                    position: "top-right",
                    timeout: 5000,
                    closeOnClick: true,
                    pauseOnHover: true,
                });
            }
        },
        onError: (page) => {
            if (page.props?.flash?.error) {
                toast.error(page.props.flash.error, {
                    position: "top-right",
                    timeout: 5000,
                    closeOnClick: true,
                    pauseOnHover: true,
                });
            }
        },
    });
}

function showToast(message) {
    actionToastMessage.value = message;
    showActionToast.value = true;
    setTimeout(() => {
        showActionToast.value = false;
    }, 3000);
}
</script>

<style lang="scss" scoped>

$primary-color: #4361ee;
$secondary-color: #3f4254;
$success-color: #0abb87;
$danger-color: #f64e60;
$warning-color: #ffa800;
$info-color: #3699ff;
$light-color: #f3f6f9;
$dark-color: #181c32;
$border-color: #e4e6ef;
$body-color: #f9fafb;
$card-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
$hover-transition: all 0.3s ease;
$border-radius: 0.475rem;

.content-wrapper {
    background-color: $body-color;
    min-height: calc(100vh - 65px); 
    padding: 2rem 0;
}

.page-header {
    margin-bottom: 1.5rem;

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid $border-color;
    }

    .header-title {
        .page-title {
            margin: 0;
            font-weight: 600;
            color: $secondary-color;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .text-muted {
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }
    }


    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }


    .search-container {
        position: relative;
        width: 250px;

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a5b7;
            pointer-events: none;
        }

        input {
            padding-right: 35px;
            padding-left: 12px;
            border-radius: 8px;
            border: 1px solid $border-color;
            height: 42px;

            &:focus {
                border-color: $primary-color;
                box-shadow: 0 0 0 0.25rem rgba($primary-color, 0.1);
            }
        }
    }

    .permission-multiselect {
        position: relative;
        width: 220px;

        .permission-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid $border-color;
            background-color: white;
            height: 42px;
            cursor: pointer;
            font-size: 0.875rem;
            color: $secondary-color;
            text-align: left;
            transition: $hover-transition;

            &:hover, &:focus {
                border-color: $primary-color;
                outline: none;
            }

            span {
                flex: 1;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }

        .permission-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid $border-color;
            border-radius: $border-radius;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 100;
            max-height: 260px;
            overflow-y: auto;
            padding: 0.25rem 0;

            .permission-option {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.4rem 0.75rem;
                cursor: pointer;
                font-size: 0.875rem;
                color: $secondary-color;
                margin: 0;
                font-weight: normal;

                &:hover {
                    background-color: rgba($primary-color, 0.05);
                    color: $primary-color;
                }

                input[type="checkbox"] {
                    cursor: pointer;
                    accent-color: $primary-color;
                }
            }
        }
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        height: 42px;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        border: none;
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        white-space: nowrap;

        &:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #4338ca 0%, #2563eb 100%);
            color: white;
        }

        &:active {
            transform: translateY(0);
        }

        i {
            font-size: 1.1rem;
            color: white;
        }
    }
}

.data-card {
    background-color: white;
    border-radius: $border-radius;
    box-shadow: $card-shadow;
    border: none;
    overflow: hidden;
    margin-bottom: 2rem;

    .card-body {
        padding: 0;
    }
}

.table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid $border-color;
    flex-wrap: wrap;
    gap: 1rem;

    .table-info {
        display: flex;
        align-items: center;
        gap: 1rem;

        .result-count {
            color: $secondary-color;
            font-size: 0.95rem;

            strong {
                color: $primary-color;
                font-weight: 600;
            }
        }

        .active-filter-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: rgba($primary-color, 0.1);
            color: $primary-color;
            padding: 0.5rem 0.75rem;
            border-radius: $border-radius;
            font-size: 0.85rem;
            font-weight: 500;

            .btn-clear-inline {
                background: none;
                border: none;
                color: $danger-color;
                cursor: pointer;
                padding: 0.25rem;
                margin-left: 0.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: $hover-transition;

                &:hover {
                    background-color: rgba($danger-color, 0.1);
                }
            }
        }
    }

    .table-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: $border-radius;
            border: 1px solid $border-color;
            background-color: white;
            color: $secondary-color;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: $hover-transition;

            &:hover {
                background-color: $light-color;
                color: $primary-color;
                border-color: $primary-color;
            }
        }

        .view-selector {
            display: flex;
            align-items: center;
            margin-left: 0.5rem;

            .view-btn {
                width: 32px;
                height: 32px;
                border: 1px solid $border-color;
                background-color: white;
                color: $secondary-color;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: $hover-transition;

                &:first-child {
                    border-radius: $border-radius 0 0 $border-radius;
                    border-right: none;
                }

                &:last-child {
                    border-radius: 0 $border-radius $border-radius 0;
                }

                &:hover {
                    background-color: $light-color;
                    color: $primary-color;
                }

                &.active {
                    background-color: $primary-color;
                    color: white;
                    border-color: $primary-color;
                }
            }
        }
    }
}

.custom-table {
    width: 100%;
    margin-bottom: 0;

    th {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid $border-color;
        font-weight: 600;
        color: $secondary-color;
        background-color: white;
        white-space: nowrap;

        .table-header {
            display: flex;
            align-items: center;
            user-select: none;
            cursor: pointer;

            &:hover {
                color: $primary-color;
            }
        }
    }

    td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid $border-color;
    }

    .user-row {
        transition: $hover-transition;

        &:hover {
            background-color: rgba($primary-color, 0.03);
        }
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;

        .user-avatar {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            border: 2px solid white;

            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .user-status {
                position: absolute;
                bottom: 0;
                right: 0;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: $danger-color;
                border: 2px solid white;

                &.active {
                    background-color: $success-color;
                }
            }
        }

        .user-details {
            display: flex;
            flex-direction: column;

            .user-name {
                font-weight: 500;
                color: $dark-color;
                margin-bottom: 0.25rem;
            }

            .user-role {
                font-size: 0.8rem;
                color: $primary-color;
                background-color: rgba($primary-color, 0.1);
                padding: 0.125rem 0.5rem;
                border-radius: 20px;
                display: inline-block;
            }
        }
    }

    .user-permissions-cell {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        align-items: center;

        .role-badge {
            display: inline-block;
            font-size: 0.72rem;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            background-color: rgba($primary-color, 0.1);
            color: $primary-color;
            font-weight: 500;
            white-space: nowrap;

            &.role-badge-more {
                background-color: rgba($secondary-color, 0.1);
                color: $secondary-color;
            }
        }
    }


    .user-permissions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;

        .permission-badge {
            font-size: 0.8rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            background-color: rgba($secondary-color, 0.1);
            color: $secondary-color;

            &.admin {
                background-color: rgba($primary-color, 0.1);
                color: $primary-color;
                font-weight: 500;
            }

            &.editor {
                background-color: rgba($info-color, 0.1);
                color: $info-color;
            }

            &.viewer {
                background-color: rgba($success-color, 0.1);
                color: $success-color;
            }
        }
    }

    .user-email,
    .user-phone {
        color: $secondary-color;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: $hover-transition;

        &:hover {
            color: $primary-color;
        }

        .email-icon,
        .phone-icon {
            font-size: 0.85rem;
            color: rgba($secondary-color, 0.7);
        }
    }

    .gender-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;

        &.male {
            background-color: rgba($info-color, 0.1);
            color: $info-color;

            .gender-icon {
                color: $info-color;
            }
        }

        &.female {
            background-color: rgba($primary-color, 0.1);
            color: $primary-color;

            .gender-icon {
                color: $primary-color;
            }
        }
    }

    .action-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;

        .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: $hover-transition;

            &.btn-outline-primary {
                border-color: rgba($primary-color, 0.5);
                color: $primary-color;

                &:hover {
                    background-color: rgba($primary-color, 0.1);
                    border-color: $primary-color;
                    transform: translateY(-2px);
                    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
                }
            }

            &.btn-outline-danger {
                border-color: rgba($danger-color, 0.5);
                color: $danger-color;

                &:hover {
                    background-color: rgba($danger-color, 0.1);
                    border-color: $danger-color;
                    transform: translateY(-2px);
                    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
                }
            }

            &.btn-outline-secondary {
                border-color: rgba($secondary-color, 0.5);
                color: $secondary-color;

                &:hover {
                    background-color: rgba($secondary-color, 0.1);
                    border-color: $secondary-color;
                    transform: translateY(-2px);
                    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
                }
            }

            &.btn-outline-info {
                border-color: rgba($info-color, 0.5);
                color: $info-color;

                &:hover {
                    background-color: rgba($info-color, 0.1);
                    border-color: $info-color;
                    transform: translateY(-2px);
                    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
                }
            }
        }

        .dropdown-menu {
            min-width: 180px;
            padding: 0.5rem 0;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
            border: 1px solid $border-color;
            border-radius: $border-radius;

            .dropdown-item {
                padding: 0.5rem 1rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: $secondary-color;
                font-size: 0.95rem;
                transition: $hover-transition;

                i {
                    width: 16px;
                    text-align: center;
                }

                &:hover {
                    background-color: $light-color;
                }
            }
        }
    }
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
    color: #6c757d;
}

.text-customer {
    font-size: 0.65rem;
    color: $success-color;
    font-weight: 700;
    text-transform: uppercase;
    background: rgba($success-color, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 6px;
    vertical-align: middle;
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-top: 1px solid $border-color;
    flex-wrap: wrap;
    gap: 1rem;

    .pagination-info {
        color: $secondary-color;
        font-size: 0.9rem;
    }

    .pagination {
        margin: 0;

        .page-item {
            &.active .page-link {
                background-color: $primary-color;
                border-color: $primary-color;
                color: white;
                font-weight: 500;
            }

            &.disabled .page-link {
                color: #c2c6d1;
                pointer-events: none;
            }
        }

        .page-link {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 0.25rem;
            margin: 0 0.25rem;
            color: $secondary-color;
            border: 1px solid $border-color;

            &:hover {
                background-color: $light-color;
                border-color: $border-color;
                color: $primary-color;
                z-index: 1;
            }
        }

        .page-ellipsis {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: $secondary-color;
            background-color: white;
            border: 1px solid $border-color;
            border-radius: 0.25rem;
            margin: 0 0.25rem;
        }
    }
}

.action-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: $success-color;
    color: white;
    padding: 0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    animation: fadeInOut 3s ease-in-out;
    z-index: 1000;
    overflow: hidden;

    .action-toast-content {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        position: relative;

        &::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: rgba(255, 255, 255, 0.4);
        }
    }
}

@keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }

    10% {
        opacity: 1;
        transform: translateY(0);
    }

    80% {
        opacity: 1;
        transform: translateY(0);
    }

    100% {
        opacity: 0;
        transform: translateY(20px);
    }
}

@media (max-width: 767.98px) {
    .page-header {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            justify-content: space-between;

            .search-container {
                width: 55%;
            }

            .btn-add {
                width: 40%;
                justify-content: center;

                span {
                    display: none;
                }

                i {
                    margin-right: 0;
                }
            }
        }
    }

    .table-toolbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;

        .table-filters {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;

            .filter-btn {
                white-space: nowrap;
            }
        }

        .table-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }

    .table-responsive {
        .user-info {
            .user-details {
                .user-role {
                    font-size: 0.75rem;
                }
            }
        }

        .gender-badge {
            font-size: 0.75rem;
            padding: 0.125rem 0.5rem;
        }

        .user-permissions {
            .permission-badge {
                font-size: 0.7rem;
            }
        }

        .action-buttons {
            flex-wrap: wrap;
            justify-content: flex-start;

            .btn {
                margin-bottom: 0.25rem;
            }
        }
    }

    .pagination-container {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;

        .pagination-info {
            text-align: center;
        }
    }
}
</style>
