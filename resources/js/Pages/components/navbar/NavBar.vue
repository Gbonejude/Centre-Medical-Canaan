<template>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <Link class="navbar-logo me-3 d-flex" :href="route('home.index')">
                <img :src="LogoCCS" alt="logoCCS">
                </Link>

                <div class="navbar-nav-section d-none d-lg-flex justify-content-between align-items-center">
                    <Link :href="route('home.index')" class="btn-primary-blue mx-4"
                        :class="{ 'active': route().current('home.index') && !$page.url.includes('#') }">
                    Accueil
                    </Link>
                    <Link :href="route('home.index') + '#services'" class="btn-primary-blue mx-4"
                        :class="{ 'active': $page.url.includes('#services') }">
                    Services
                    </Link>

                    <Link :href="route('home.index') + '#faq'" class="btn-primary-blue mx-4"
                        :class="{ 'active': $page.url.includes('#faq') }">
                    FAQ
                    </Link>

                    
                    <div class="auth-section d-none d-lg-block">
                        <Link :href="route('auth.loginForm')" class="btn btn-primary ms-4 px-5 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se Connecter
                        </Link>
                    </div>
                </div>

                <button class="navbar-toggler d-lg-none" type="button" @click="toggleMobileMenu"
                    :aria-expanded="isMobileMenuOpen">
                    <i class="fas fa-bars" v-if="!isMobileMenuOpen"></i>
                    <i class="fas fa-times" v-else></i>
                </button>
            </div>

            
            <div class="mobile-menu d-lg-none" :class="{ 'show': isMobileMenuOpen }">
                
                <div class="mobile-nav-links">
                    <Link :href="route('home.index')" class="mobile-nav-link" @click="closeMobileMenu">
                    <i class="fas fa-home me-2"></i>Accueil
                    </Link>
                    <Link :href="route('home.index') + '#services'" class="mobile-nav-link" @click="closeMobileMenu">
                    <i class="fas fa-stethoscope me-2"></i>Services
                    </Link>
                    <Link :href="route('home.index') + '#faq'" class="mobile-nav-link" @click="closeMobileMenu">
                    <i class="fas fa-question-circle me-2"></i>FAQ
                    </Link>
                </div>

                
                <div class="mobile-auth-section">
                    <Link :href="route('auth.loginForm')" class="mobile-login-dropdown" @click="closeMobileMenu">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Se Connecter
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'

const isMobileMenuOpen = ref(false)
const LogoCCS = '/assets/img/logo.png'

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false
}

const handleResize = () => {
    if (window.innerWidth >= 992) { 
        closeMobileMenu()
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})
</script>

<style>
:root {
    --primary-green: #28a745;
    --primary-blue: #3b5998;
    --light-bg: #f8f9fa;
    --orange-bg: #fff5eb;
}

.navbar {
    background-color: #fff !important;
    padding: 1rem 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.navbar-logo {
    width: 60px;
    height: 60px;
}

.navbar-logo img {
    width: 100%;
    object-fit: contain;
}

.btn-primary {
    background-color: #3b5998 !important;
    color: #fff !important;
    border-radius: 25px !important;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(59, 89, 152, 0.3);
}

.btn-primary-blue {
    color: #2d2d2d !important;
    font-size: 1rem;
    border: none;
    background: transparent;
    text-decoration: none;
}

.btn-primary-blue.active {
    color: #3b5998 !important;
    font-weight: 600;
}

.btn-primary-blue:hover {
    color: #3b5998 !important;
    transform: scale(1.1);
}

.navbar-toggler {
    background: none;
    border: 2px solid var(--primary-blue);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    color: var(--primary-blue);
}

.mobile-menu {
    width: 100%;
    background: white;
    border-top: 1px solid #e9ecef;
    margin-top: 1rem;
    padding: 1rem 0;
    display: none;
}

.mobile-menu.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.mobile-nav-links {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.mobile-nav-link {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    color: #2d2d2d;
    text-decoration: none;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.mobile-login-dropdown {
    width: 100%;
    padding: 0.75rem 1.5rem;
    color: #3b5998;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .navbar { padding: 0.75rem 1rem; }
    .navbar-logo { width: 50px; height: 50px; }
}
</style>
