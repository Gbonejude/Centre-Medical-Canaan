<template>
    <div class="header">
        <div class="header-left">
            <Link :href="route('dashboard.index')" class="logo">
            <img src="/assets/img/logo.png" width="35" height="35" alt="" /> <span>CCS / LHC</span>
            </Link>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fas fa-bars"></i></a>
        <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fas fa-bars"></i></a>

        <ul class="nav user-menu float-right d-none d-sm-flex align-items-center">
            <li class="nav-item dropdown me-3">
                <a href="#" class="dropdown-toggle nav-link position-relative d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" style="width: 35px; height: 35px;">
                    <i class="far fa-bell" style="font-size: 18px;"></i>
                    <span v-if="notifications.length > 0" class="badge rounded-pill bg-danger position-absolute"
                        style="top: -2px; right: -2px; font-size: 10px; padding: 2px 4px; border: 2px solid white;">
                        {{ notifications.length }}
                    </span>
                </a>
                 <div class="dropdown-menu notifications">
                    <div class="topnav-dropdown-header">
                        <span>Notifications</span>
                    </div>
                    <div class="drop-scroll"  >
                        <ul class="notification-list">
                            <li class="notification-message"  v-if="notifications.length > 0" >
                                <div v-for="notification in notifications" :key="notification.id"
                                    @click="handleHeaderNotificationClick(notification)"
                                    class="notification-link" style="cursor: pointer; display: block; padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                    <div class="media" style="display: flex;">
                                        <span class="avatar">
                                            <span class="user-img position-relative">
                                                <span
                                                    class="avatar-initials rounded-circle d-flex align-items-center justify-content-center"
                                                    :style="userAvatarStyle"
                                                    style="width: 40px; height: 40px; font-size: 13px; font-weight: 600; color: white;">
                                                    {{ userInitials }}
                                                </span>
                                            </span>
                                        </span>
                                        <div class="media-body" style="margin-left: 10px;">
                                            <p class="noti-details" style="margin-bottom: 0;"><span class="noti-title" style="font-weight: 600;">{{ notification.data.title
                                                    }}</span> </p>
                                            <p class="noti-details" style="margin-bottom: 0;"><span class="noti-title">{{
                                                    truncateText(notification.data.message,40) }}</span></p>
                                            <p class="noti-time" style="margin-bottom: 0;"><span
                                                    class="notification-time" style="font-size: 12px; color: #999;">{{ formatDate(notification.created_at)
                                                    }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li v-if="notifications.length === 0" class="notification-message text-center p-3">
                                <span>Aucune notification pour le moment ??</span>
                            </li>
                        </ul>
                    </div>
                    <div class="topnav-dropdown-footer">
                        <Link :href="route('notifications.index')">View all Notifications</Link>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                    <span class="user-img position-relative">
                        <img v-if="userAvatar" :src="userAvatar" :alt="`${user.firstname} ${user.lastname}`"
                            class="avatar-image rounded-circle shadow-sm"
                            style="width: 40px; height: 40px; object-fit: cover; border: 1.5px solid rgba(255, 255, 255, 0.3);"
                            @error="handleAvatarError" />
                        <span v-else
                            class="avatar-initials rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            :style="userAvatarStyle"
                            style="width: 40px; height: 40px; font-size: 13px; font-weight: 600; color: white; border: 1.5px solid rgba(255, 255, 255, 0.3);">
                            {{ userInitials }}
                        </span>
                    </span>
                </a>

                <div class="dropdown-menu">
                    <Link :href="route('profile.index')" class="dropdown-item">
                    <i class="fa fa-user me-2"></i> Mon Profil
                    </Link>
                    <button class="dropdown-item" @click="submitLogout">
                        <i class="fa fa-sign-out-alt me-2"></i>Déconnexion</button>
                </div>
            </li>
        </ul>
        <div class="d-flex d-sm-none align-items-center" style="position: absolute; right: 0; top: 0; height: 50px; color: #fff; z-index: 10; padding: 0 10px; gap: 8px;">
            <!-- Mobile Notifications -->
            <div class="dropdown me-3">
                <a href="#" class="dropdown-toggle nav-link p-0 position-relative d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" style="width: 35px; height: 35px;">
                    <i class="far fa-bell" style="font-size: 20px;"></i>
                    <span v-if="notifications.length > 0" class="badge rounded-pill bg-danger position-absolute"
                        style="top: -2px; right: -2px; font-size: 10px; padding: 2px 4px; border: 1.5px solid white;">
                        {{ notifications.length }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notifications">
                    <div class="topnav-dropdown-header">
                        <span>Notifications</span>
                    </div>
                    <div class="drop-scroll">
                        <ul class="notification-list">
                            <li class="notification-message" v-if="notifications.length > 0">
                                <div v-for="notification in notifications" :key="notification.id"
                                    @click="handleHeaderNotificationClick(notification)" class="notification-link"
                                    style="cursor: pointer; display: block; padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                    <div class="media" style="display: flex;">
                                        <div class="media-body">
                                            <p class="noti-details" style="margin-bottom: 0;"><span class="noti-title"
                                                    style="font-weight: 600;">{{ notification.data.title
                                                    }}</span> </p>
                                            <p class="noti-details" style="margin-bottom: 0;"><span class="noti-title">{{
                                                    truncateText(notification.data.message,40) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li v-if="notifications.length === 0" class="notification-message text-center p-3">
                                <span>Aucune notification</span>
                            </li>
                        </ul>
                    </div>
                    <div class="topnav-dropdown-footer">
                        <Link :href="route('notifications.index')">Voir toutes les notifications</Link>
                    </div>
                </div>
            </div>

            <!-- Mobile User Menu -->
            <div class="dropdown">
                <a href="#" class="dropdown-toggle nav-link p-0 d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" style="width: 30px; height: 35px;">
                    <i class="fas fa-ellipsis-v" style="color: white; font-size: 18px;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <Link :href="route('profile.index')" class="dropdown-item">
                    <i class="fa fa-user me-2"></i> Mon Profil
                    </Link>
                    <button class="dropdown-item" @click="submitLogout">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useToast } from "vue-toastification";
import { useForm, usePage, router, Link } from '@inertiajs/vue3';
import moment from 'moment';
import 'moment/locale/en-gb';
import Swal from "sweetalert2";
import 'sweetalert2/dist/sweetalert2.min.css';

moment.locale('en');
const toast = useToast();
const page = usePage();
const avatarError = ref(false);

const user = computed(() => {
    if (!usePage().props.auth || !usePage().props.auth.user) {
        if (typeof window !== 'undefined') {
            window.location.href = '/login';
        }
        return null;
    }
    return usePage().props.auth.user
})

const userAvatar = computed(() => {
    if (avatarError.value) return null;
    if (user.value?.avatar_url) {
        return user.value.avatar_url;
    }
    return null;
})
const handleAvatarError = () => {
    avatarError.value = true;
}

const userInitials = computed(() => {
    if (!user.value || !user.value.firstname || !user.value.lastname) return 'U'
    const fullName = user.value.firstname + ' ' + user.value.lastname
    const names = fullName.trim().split(' ')
    if (names.length === 1) {
        return names[0].charAt(0).toUpperCase()
    }
    return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase()
})

const userAvatarStyle = computed(() => {
    return 'background: linear-gradient(135deg, #6c757d 0%, #495057 100%)'
})

const form = reactive({});

const submitLogout = () => {
    Swal.fire({
        title: "Déconnexion",
        text: "Êtes-vous sûr de vouloir vous déconnecter ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, me déconnecter",
        cancelButtonText: "Annuler",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('logout'));
        }
    })

};

const handleHeaderNotificationClick = (notification) => {
    router.post(route('notifications.markAsRead', notification.id), {}, {
        onSuccess: () => {
            if (notification.data.url) {
                router.visit(notification.data.url);
            }
        }
    });
};

const markAsRead = (id) => {
    router.post(route('notifications.markAsRead', id), {}, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                toast.success(page.props.flash.success);
            }
        }
    });
};
const notifications = computed(() => usePage().props.auth.notifications);

const formatDate = (date) => {
    return moment(date).fromNow();
};
const truncateText = (text, maxLength) => {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
};

</script>

<style scoped>
.user-link {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-img {
    display: flex;
    align-items: center;
    position: relative;
}

.status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 8px;
    height: 8px;
    background-color: green;
    border-radius: 50%;
    border: 2px solid white;
}

.avatar-initials {
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.avatar-initials:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.4);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
</style>
