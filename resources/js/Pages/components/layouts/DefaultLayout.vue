<template>
    <div class="layout">
        <nav-bar />
        <div class="main-container">
            <main class="content">
                <slot />
            </main>
        </div>
    </div>
    <footer-component />
    <PWAInstallPrompt />
</template>

<script>
import NavBar from '../navbar/NavBar.vue'
import SideBar from '../sidebar/SideBar.vue'
import FooterComponent from '../footer/Footer.vue'
import PWAInstallPrompt from '../PWAInstallPrompt.vue'

export default {
    components: {
        NavBar,
        SideBar,
        FooterComponent,
        PWAInstallPrompt
    }
}
</script>

<style>
.layout {
    display: flex;
    flex-direction: column;
    
}

.main-container {
    flex: 1;
}

.content {
    min-height: 80vh;
    width: 100%;
}
</style>
