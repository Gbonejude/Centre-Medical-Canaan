<template>
    <Transition name="pwa-fade">
        <div v-if="showBanner" class="ccs-pwa-wrapper">
            <div class="ccs-pwa-card">
                <div class="ccs-pwa-body">
                    <div class="ccs-pwa-icon-box">
                        <img
                            src="/assets/img/logo.png"
                            alt="Logo"
                            class="ccs-pwa-logo"
                        />
                    </div>
                    <div class="ccs-pwa-text">
                        <h6 class="ccs-pwa-title">Canaan Care Services</h6>
                        <p class="ccs-pwa-desc">
                            Installer Canaan Care Services sur votre écran
                            d'accueil pour un accès plus rapide.
                        </p>
                    </div>
                </div>
                <div class="ccs-pwa-footer">
                    <button
                        @click="dismissBanner"
                        class="ccs-pwa-btn-secondary"
                    >
                        Plus tard
                    </button>
                    <button @click="installApp" class="ccs-pwa-btn-primary">
                        Installer
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from "vue";

const showBanner = ref(false);
const deferredPrompt = ref(null);

const STORAGE_KEY = "ccs_pwa_installed";

const dismissBanner = () => {
    showBanner.value = false;
};

const installApp = async () => {
    if (!deferredPrompt.value) {
        return;
    }

    deferredPrompt.value.prompt();
    const { outcome } = await deferredPrompt.value.userChoice;

    if (outcome === "accepted") {
        showBanner.value = false;
        localStorage.setItem(STORAGE_KEY, "true");
    }
    deferredPrompt.value = null;
};

onMounted(() => {
    // Ne pas afficher si déjà installé (via localStorage ou via le mode d'affichage)
    if (
        localStorage.getItem(STORAGE_KEY) === "true" ||
        window.matchMedia("(display-mode: standalone)").matches ||
        window.navigator.standalone === true
    ) {
        return;
    }

    window.addEventListener("beforeinstallprompt", (e) => {
        // Empêcher l'affichage automatique du navigateur
        e.preventDefault();
        // Garder l'événement pour plus tard
        deferredPrompt.value = e;

        // Affichage après un petit délai de confort si l'application est installable
        setTimeout(() => {
            // Re-vérifier s'il n'est pas déjà installé (cas où l'utilisateur l'installe très vite)
            if (localStorage.getItem(STORAGE_KEY) !== "true") {
                showBanner.value = true;
            }
        }, 1500);
    });

    window.addEventListener("appinstalled", () => {
        showBanner.value = false;
        localStorage.setItem(STORAGE_KEY, "true");
    });
});
</script>

<style scoped>
.ccs-pwa-wrapper {
    position: fixed !important;
    bottom: 30px !important;
    right: 30px !important;
    z-index: 9999999 !important;
    display: block !important;
    pointer-events: none !important;
    width: 380px !important; /* Agrandit un peu plus */
}

.ccs-pwa-card {
    pointer-events: auto !important;
    background: #ffffff !important;
    border-radius: 16px !important;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25) !important;
    width: 100% !important;
    padding: 20px !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 16px !important;
    margin: 0 !important;
}

@media (max-width: 480px) {
    .ccs-pwa-wrapper {
        left: 20px !important;
        right: 20px !important;
        bottom: 20px !important;
        width: calc(100% - 40px) !important;
    }
}

.ccs-pwa-body {
    display: flex !important;
    align-items: center !important;
    gap: 16px !important;
}

.ccs-pwa-icon-box {
    width: 60px !important;
    height: 60px !important;
    flex-shrink: 0 !important;
}

.ccs-pwa-logo {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    border-radius: 12px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
}

.ccs-pwa-text {
    flex: 1 !important;
}

.ccs-pwa-title {
    margin: 0 !important;
    font-size: 17px !important;
    font-weight: 800 !important;
    color: #111 !important;
}

.ccs-pwa-desc {
    margin: 4px 0 0 !important;
    font-size: 13px !important;
    color: #555 !important;
    line-height: 1.4 !important;
}

.ccs-pwa-footer {
    display: flex !important;
    justify-content: flex-end !important;
    gap: 12px !important;
    border-top: 1px solid #f0f0f0 !important;
    padding-top: 14px !important;
}

.ccs-pwa-btn-secondary {
    background: transparent !important;
    color: #888 !important;
    border: none !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: color 0.2s !important;
}

.ccs-pwa-btn-secondary:hover {
    color: #444 !important;
}

.ccs-pwa-btn-primary {
    background: #3b5998 !important;
    color: #ffffff !important;
    border: none !important;
    padding: 10px 24px !important;
    border-radius: 10px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    box-shadow: 0 4px 12px rgba(59, 89, 152, 0.2) !important;
    transition: all 0.2s !important;
}

.ccs-pwa-btn-primary:hover {
    background: #2d4373 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(59, 89, 152, 0.3) !important;
}

/* Animations */
.pwa-fade-enter-active,
.pwa-fade-leave-active {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
}
.pwa-fade-enter-from,
.pwa-fade-leave-to {
    opacity: 0 !important;
    transform: translateY(20px) !important;
}
</style>
