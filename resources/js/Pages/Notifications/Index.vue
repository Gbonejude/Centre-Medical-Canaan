<template>
    <Head>
        <title>Notifications | CCS</title>
        <meta name="description" content="Voir toutes vos notifications" />
    </Head>

    <div class="content-wrapper">
        <div class="container">
            <!-- Page Header (Matches Users Management Style) -->
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h4 class="page-title">
                            <i class="far fa-bell me-2"></i>
                            Notifications
                        </h4>
                        <p class="text-muted">Restez informé de vos dernières alertes et activités</p>
                    </div>
                    <div class="header-actions">
                        <div class="search-container">
                            <input type="text" v-model="searchQuery" placeholder="Rechercher des notifications..." class="form-control" />
                            <i class="fa fa-search search-icon"></i>
                        </div>

                        <button v-if="notifications.total > 0" @click="markAllAsRead" 
                            class="btn btn-primary btn-add">
                            <i class="fas fa-check-double me-1"></i>
                            <span>Tout marquer comme lu</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifications Content -->
            <div v-if="filteredNotifications.length > 0" class="notifications-list">
                <div v-for="notification in filteredNotifications" :key="notification.id" 
                    class="notification-item" :class="{ 'unread': !notification.read_at }">
                    <div class="item-content p-4">
                        <div class="d-flex gap-4 align-items-start">
                            <div class="icon-wrapper">
                                <div class="icon-circle" :class="getNotificationTypeClass(notification)">
                                    <i :class="getNotificationIcon(notification)"></i>
                                </div>
                                <div v-if="!notification.read_at" class="pulse-dot"></div>
                            </div>
                            
                            <div class="content-main flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="type-header">
                                        <span class="type-label" :class="getNotificationTypeClass(notification)">
                                            {{ getNotificationTitle(notification) }}
                                        </span>
                                    </div>
                                    <span class="time-stamp">
                                        <i class="far fa-clock me-1"></i>
                                        {{ formatDate(notification.created_at) }}
                                    </span>
                                </div>

                                <h5 class="message-text mb-3 text-dark">{{ notification.data.message }}</h5>

                                <div class="item-footer d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                                    <div class="main-actions d-flex gap-2">
                                        <Link v-if="notification.data.url" :href="formatUrl(notification.data.url)" 
                                            class="btn btn-premium btn-view" @click="handleNotificationClick(notification)">
                                            <i class="fas fa-external-link-alt me-2"></i> Voir
                                        </Link>
                                        <button v-if="!notification.read_at" @click="markAsRead(notification.id)" 
                                            class="btn btn-premium btn-mark">
                                            <i class="fas fa-check me-2"></i> Marquer comme lu
                                        </button>
                                    </div>
                                    <button @click="deleteNotification(notification.id)" 
                                        class="btn-delete-ghost" title="Remove notification">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination (Matches Users Management style info placement) -->
                <div class="pagination-container mt-4" v-if="notifications.last_page > 1">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li :class="['page-item', { disabled: notifications.current_page === 1 }]">
                                <Link :href="getPageUrl(notifications.current_page - 1)" class="page-link">
                                    <i class="fa fa-chevron-left"></i>
                                </Link>
                            </li>

                            <li v-for="page in paginationRange" :key="page"
                                :class="['page-item', { active: page === notifications.current_page }]">
                                <Link v-if="page !== '...'" :href="getPageUrl(page)" class="page-link">
                                    {{ page }}
                                </Link>
                                <span v-else class="page-ellipsis">...</span>
                            </li>

                            <li :class="['page-item', { disabled: notifications.current_page === notifications.last_page }]">
                                <Link :href="getPageUrl(notifications.current_page + 1)" class="page-link">
                                    <i class="fa fa-chevron-right"></i>
                                </Link>
                            </li>
                        </ul>
                    </nav>
                    <div class="pagination-info text-muted small">
                        Affichage de <span class="fw-bold text-dark">{{ (notifications.current_page - 1) * notifications.per_page + 1 }}</span>
                        à
                        <span class="fw-bold text-dark">{{ Math.min(notifications.current_page * notifications.per_page, notifications.total) }}</span> sur
                        <span class="fw-bold text-dark">{{ notifications.total }}</span> entrées
                    </div>
                </div>
            </div>

            <div v-else class="empty-state-card text-center py-5 shadow-sm rounded-4 bg-white border mt-4">
                <div class="empty-icon-box mb-4 mx-auto">
                    <i class="far fa-bell-slash"></i>
                </div>
                <h3 class="fw-bold text-dark">Tout est à jour !</h3>
                <p class="text-muted mx-auto" style="max-width: 300px;">Vous avez traité toutes vos notifications. Bon travail !</p>
                <Link :href="route('dashboard.index')" class="btn btn-primary px-5 py-3 rounded-pill fw-bold mt-4 shadow-sm">
                    Aller au Tableau de Bord
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import moment from 'moment';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2';

const toast = useToast();
const searchQuery = ref('');

const props = defineProps({
    notifications: {
        type: Object,
        required: true
    }
});

const formatUrl = (url) => {
    if (!url) return '#';
    // If the URL contains the production domain, strip it to make it relative
    // This fixes CORS/redirection issues for old notifications stored with absolute URLs
    return url.replace(/^https?:\/\/admin\.canaanapp\.com/, '');
};

const formatDate = (date) => {
    return moment(date).fromNow();
};

const getPageUrl = (page) => {
    return `${route('notifications.index')}?page=${page}`;
};

const filteredNotifications = computed(() => {
    if (!searchQuery.value) return props.notifications.data;
    const query = searchQuery.value.toLowerCase();
    return props.notifications.data.filter(n => 
        n.data.message.toLowerCase().includes(query) || 
        (n.data.title && n.data.title.toLowerCase().includes(query))
    );
});

const paginationRange = computed(() => {
    const currentPage = props.notifications.current_page;
    const lastPage = props.notifications.last_page;

    if (lastPage <= 7) {
        return Array.from({ length: lastPage }, (_, i) => i + 1);
    }

    let range = [];
    range.push(1);

    if (currentPage <= 3) {
        range.push(2, 3, 4, 5, '...', lastPage);
    } else if (currentPage >= lastPage - 2) {
        range.push('...', lastPage - 4, lastPage - 3, lastPage - 2, lastPage - 1, lastPage);
    } else {
        range.push('...', currentPage - 1, currentPage, currentPage + 1, '...', lastPage);
    }

    return range;
});

const getNotificationTypeClass = (notification) => {
    const title = notification.data.title || '';
    if (title.toLowerCase().includes('device')) return 'type-device';
    
    switch (notification.data.type) {
        case 'mention': return 'type-mention';
        case 'permission': return 'type-permission';
        case 'message': return 'type-message';
        default: return 'type-default';
    }
};

const getNotificationIcon = (notification) => {
    const title = notification.data.title || '';
    if (title.toLowerCase().includes('device')) return 'fas fa-shield-alt';

    switch (notification.data.type) {
        case 'mention': return 'fas fa-at';
        case 'permission': return 'fas fa-bullhorn';
        case 'message': return 'fas fa-envelope';
        default: return 'fas fa-bell';
    }
};

const getNotificationTitle = (notification) => {
    if (notification.data.title) return notification.data.title;
    
    switch (notification.data.type) {
        case 'mention': return 'Mentionné';
        case 'permission': return 'Communauté';
        case 'message': return 'Message';
        default: return 'Notification';
    }
};

const handleNotificationClick = (notification) => {
    if (!notification.read_at) {
        markAsRead(notification.id, false);
    }
};

const markAsRead = (id, redirect = true) => {
    router.post(route('notifications.markAsRead', id), {}, {
        onSuccess: () => {
            if (redirect) toast.success('Marquée comme lue');
        }
    });
};

const markAllAsRead = () => {
    router.post(route('notifications.markAllAsRead'), {}, {
        onSuccess: () => {
            toast.success('Toutes les notifications ont été marquées comme lues');
        }
    });
};

const deleteNotification = (id) => {
    Swal.fire({
        title: "Confirmer la suppression",
        text: "Êtes-vous sûr de vouloir supprimer définitivement cette notification ? Cette action est irréversible.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimer",
        cancelButtonText: "Annuler",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('notifications.destroy', id), {
                onSuccess: () => {
                    toast.success('Notification supprimée');
                }
            });
        }
    });
};
</script>

<style lang="scss" scoped>
/* Redesigned Variables to match Users Management if needed */
$primary-color: #4361ee;
$border-color: #e4e6ef;

.content-wrapper {
    background-color: #f9fafb;
    min-height: calc(100vh - 65px);
    padding: 3rem 0; /* Increased from 2rem to 3rem for more breathing room */
}

/* Page Header (Matching Users Management) */
.page-header {
    margin-top: 1.5rem; /* Added margin-top to separate further from top bar */
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid $border-color;
}

.header-title .page-title {
    margin: 0;
    font-weight: 700;
    color: #1e293b;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
}

.header-title .text-muted {
    margin-top: 0.25rem;
    font-size: 0.875rem;
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
}

.search-container .search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-container input {
    padding-right: 35px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 0.9rem;
    height: 42px;
}

.btn-add {
    display: flex;
    align-items: center;
    padding: 0 1.25rem;
    height: 42px;
    border-radius: 10px;
    background: $primary-color;
    border: none;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.btn-add:hover {
    background: #3651d4;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba($primary-color, 0.3);
}

/* Notifications Items */
.notification-item {
    background: white;
    border-radius: 16px;
    margin-bottom: 16px;
    border: 1px solid #edf2f7;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.04);
}

.notification-item.unread {
    background: #fcfdff;
    border-left: 5px solid $primary-color;
}

.icon-wrapper {
    position: relative;
    flex-shrink: 0;
}

.icon-circle {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.pulse-dot {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 12px;
    height: 12px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
}

.type-label {
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.type-mention { background: #e0e7ff; color: $primary-color; }
.type-permission { background: #ecfdf5; color: #059669; }
.type-message { background: #eff6ff; color: #2563eb; }
.type-device { background: #fff7ed; color: #ea580c; }
.type-default { background: #f8fafc; color: #64748b; }

.icon-circle.type-mention { background: #f5f7ff; color: $primary-color; }
.icon-circle.type-permission { background: #f0fdf4; color: #059669; }
.icon-circle.type-message { background: #f0f9ff; color: #2563eb; }
.icon-circle.type-device { background: #fffaf0; color: #ea580c; border: 1px solid #ffedd5; }
.icon-circle.type-default { background: #f8fafc; color: #64748b; }

.time-stamp {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 500;
}

.message-text {
    font-size: 1.05rem;
    font-weight: 600;
    color: #1a202c;
    line-height: 1.5;
}

.btn-premium {
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    transition: all 0.2s ease;
    border: none;
}

.btn-view {
    background: #f1f5f9;
    color: $primary-color;
}

.btn-view:hover {
    background: $primary-color;
    color: white;
}

.btn-mark {
    background: #f1f5f9;
    color: #059669;
}

.btn-mark:hover {
    background: #059669;
    color: white;
}

.btn-delete-ghost {
    background: transparent;
    border: none;
    color: #cbd5e1;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.btn-delete-ghost:hover {
    background: #fff1f2;
    color: #e11d48;
}

/* Pagination (Matches Users Management) */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination {
    margin: 0;
    gap: 5px;
    display: flex;
    list-style: none;
}

.page-link, .page-ellipsis {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    text-decoration: none;
    color: #64748b;
    font-weight: 600;
    background: white;
    border: 1px solid #e2e8f0;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.page-link:hover {
    background: #f1f5f9;
    color: $primary-color;
    border-color: $primary-color;
}

.page-item.active .page-link {
    background: $primary-color;
    color: white;
    border-color: $primary-color;
}

.page-item.disabled .page-link {
    opacity: 0.3;
    pointer-events: none;
}

.empty-icon-box {
    width: 100px;
    height: 100px;
    background: #f1f5f9;
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
    color: #cbd5e1;
}
</style>
