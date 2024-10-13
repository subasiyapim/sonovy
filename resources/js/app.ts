import '../css/app.css';
import './bootstrap';
import '../css/general.scss';

import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createApp, DefineComponent, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';
import debounce from './Directives/Debounce';
import clickOutside from './Directives/ClickOutside';
import {createPinia} from 'pinia';
import __ from "./libs/locale";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import VueTippy from 'vue-tippy'
// or
import 'tippy.js/dist/tippy.css' // optional for styling


import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .directive('debounce', (el, binding) => debounce(el, binding))
            .directive('click-outside', clickOutside)
            .component('VueDatePicker', VueDatePicker)
            .use(VueTippy,
                // optional
                {
                    directive: 'tippy', // => v-tippy
                    component: 'tippy', // => <tippy/>
                    componentSingleton: 'tippy-singleton', // => <tippy-singleton/>,
                    defaultProps: {
                        placement: 'auto-end',
                        allowHTML: true,
                    }, // => Global default options * see all props
                }
            )
            .use(Vue3Toastify,
            {
                autoClose: 3000,
                hideProgressBar:true,
            })
            .use(plugin)
            .use(ZiggyVue)
            .use(createPinia())
            .use(__)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
