import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Notifications from 'notiwind'
import mitt from 'mitt';
import PrimeVue from 'primevue/config';
import Vapor from 'laravel-vapor'

const emitter = mitt();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .provide('emitter', emitter)
            .use(Notifications)
            .use(PrimeVue, {
                theme: 'none',
                unstyled: true,
                //pt: PrimeCETO
            })
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        color: '#FDDB5D',
    },
});
