<template>
  <div class="main-wrapper">
    <Header />
    <SideBar />
    <div class="page-wrapper">
      <slot />
    </div>
    <PWAInstallPrompt />
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import Header from "../backoffice/header/index.vue";
import SideBar from "../backoffice/sidebar/index.vue";
import PWAInstallPrompt from "../PWAInstallPrompt.vue";

import '../../../../../public/assets/css/dataTables.bootstrap4.min.css'
import '../../../../../public/assets/css/style.css'

onMounted(() => {
  loadScriptsSequentially();
});

function loadScriptsSequentially() {
  const scripts = [
    "/assets/js/jquery-3.2.1.min.js",
    "/assets/js/popper.min.js",
    "/assets/js/bootstrap.min.js",
    "/assets/js/jquery.slimscroll.js",
    "/assets/js/app.js"
  ];

  function loadScript(index) {
    if (index >= scripts.length) return;

    const script = document.createElement("script");
    script.src = scripts[index];
    script.onload = () => {
      console.log(`Script loaded: ${scripts[index]}`);
      loadScript(index + 1); 
    };
    script.onerror = () => {
      console.error(`Failed to load script: ${scripts[index]}`);
      loadScript(index + 1); 
    };
    document.body.appendChild(script);
  }

  loadScript(0);
}
</script>

<style scoped>
</style>
