import './bootstrap.js'
import { createApp, h, nextTick } from 'vue'
import { createInertiaApp, Link, Head, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Ziggy } from './ziggy.js';
import { route } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import "vue-toastification/dist/index.css";
import 'sweetalert2/dist/sweetalert2.min.css';
import AdminLayout from './Pages/components/layouts/AdminLayout.vue';
import DefaultLayout from './Pages/components/layouts/DefaultLayout.vue';
import AOS from 'aos'
import 'aos/dist/aos.css'

router.on('error', (event) => {
    if (event.detail?.response?.status === 401 || event.detail?.response?.status === 419) {
        window.location.href = '/login';
    }
});

router.on('before', (event) => {
    if (event.detail.visit.response?.props?.errors?.message === 'Unauthenticated.') {
        window.location.href = '/login';
        return false; 
    }
});

createInertiaApp({
    progress: {
        delay: 250,
        color: '#ff0000',
        includeCSS: true,
    },

    resolve: async name => {
        const pageComponent = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
        const page = pageComponent.default;

        let layout = null;

        if (name.toLocaleLowerCase().startsWith('auth/')) {
            layout = null;
        } else if (name.toLocaleLowerCase().startsWith('frontoffice/')) {
            layout = DefaultLayout;
        } else {
            layout = AdminLayout;
        }

        page.layout = page.layout !== undefined ? page.layout : layout;

        return pageComponent;
    },

    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Toast)
            .mixin({ methods: { route } })
            .component('Link', Link)
            .component('Head', Head)
            .mount(el)

        nextTick(() => {
            AOS.init({
                once: true,
                duration: 800, 
                easing: 'ease-in-out'
            })
        })

        return vueApp
    },

    title: title => `Centre Médical Canaan - ${title}`,
})
