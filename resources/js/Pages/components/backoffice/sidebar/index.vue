<template>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <!-- Dashboard -->
                    <li class="mt-3" v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])" :class="{ active: isActiveLink('/') }">
                        <Link :href="route('dashboard.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-dashboard"></i><span>Tableau de Bord</span>
                        </Link>
                    </li>

                    <!-- User Management (Admin only) -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN])"
                        :class="{ active: isActiveLink('/users') }"
                    >
                        <Link :href="route('users.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-users-cog"></i><span>Utilisateurs</span>
                        </Link>
                    </li>

                    <!-- ── HÔPITAL ── -->
                    <li class="menu-title"
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST, PERMISSIONS.DOCTOR])">
                        Hôpital
                    </li>

                    <!-- Services Médicaux -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/medical-services') }"
                    >
                        <Link :href="route('medical-services.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-hospital"></i><span>Services Médicaux</span>
                        </Link>
                    </li>

                    <!-- Spécialités -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/specialties') }"
                    >
                        <Link :href="route('specialties.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-award"></i><span>Spécialités</span>
                        </Link>
                    </li>

                    <!-- Médecins -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/doctors') }"
                    >
                        <Link :href="route('doctors.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-user-md"></i><span>Médecins</span>
                        </Link>
                    </li>

                    <!-- Patients -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/patients') }"
                    >
                        <Link :href="route('patients.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-procedures"></i><span>Patients</span>
                        </Link>
                    </li>

                    <!-- Rendez-vous (Admin & Réceptionniste) -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/appointments') }"
                    >
                        <Link :href="route('appointments.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-calendar-check"></i><span>Rendez-vous</span>
                        </Link>
                    </li>

                    <!-- Planning des Médecins (Admin & Réceptionniste) -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/availabilities') }"
                    >
                        <Link :href="route('schedules.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-calendar-day"></i><span>Disponibilités</span>
                        </Link>
                    </li>

                    <!-- Mon Planning (Médecin Uniquement) -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.DOCTOR]) && !userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/availabilities') }"
                    >
                        <Link :href="route('schedules.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-calendar-alt"></i><span>Mes Disponibilités</span>
                        </Link>
                    </li>

                    <!-- Mes Rendez-vous (Médecin Uniquement) -->
                    <li
                        v-if="userHasPermission([PERMISSIONS.DOCTOR]) && !userHasPermission([PERMISSIONS.SUPER_ADMIN, PERMISSIONS.ADMIN, PERMISSIONS.RECEPTIONIST])"
                        :class="{ active: isActiveLink('/appointments') }"
                    >
                        <Link :href="route('appointments.index')" @click="closeSidebarOnMobile">
                            <i class="fa fa-user-md"></i><span>Mes Rendez-vous</span>
                        </Link>
                    </li>


                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import { userHasPermission, isActiveLink } from "../../../../utils/auth";
import { PERMISSIONS } from "../../../../constants/permission.js";

const closeSidebarOnMobile = () => {
    if (window.innerWidth <= 991) {
        const t = document.querySelector(".mobile_btn");
        if (t) t.click();
    }
};

const isDocumentOpen = ref(false);
const isMessagingOpen = ref(false);

const isDocumentMenuOpen = computed(() =>
    isDocumentOpen.value || isActiveLink("/document-types") || isActiveLink("/documents")
);
const isMessagingMenuOpen = computed(() =>
    isMessagingOpen.value || isActiveLink("/conversations") || isActiveLink("/my-conversations")
);

const toggleDocumentMenu = () => { isDocumentOpen.value = !isDocumentOpen.value; };
const toggleMessagingMenu = () => { isMessagingOpen.value = !isMessagingOpen.value; };

onMounted(() => {
    const s = document.querySelector(".sidebar-inner");
    if (s) { s.style.overflowY = "auto"; s.style.height = "100vh"; }
});
</script>

<style scoped>
.sidebar-inner { height: 100vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #ccc #f1f1f1; }
.sidebar-inner::-webkit-scrollbar { width: 4px; }
.sidebar-inner::-webkit-scrollbar-thumb { background: #ccc; border-radius: 2px; }
.submenu.open ul { display: block; padding-left: 20px; }
.submenu ul { display: none; }
.submenu.open .menu-arrow::after { transform: rotate(180deg); }
.menu-arrow::after { float: right; transition: transform 0.3s ease; }
</style>
