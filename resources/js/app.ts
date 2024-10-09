import '../css/app.css';
import './bootstrap';
import '../css/general.scss';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import debounce from './Directives/Debounce';
import clickOutside from './Directives/ClickOutside';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import VueTippy from 'vue-tippy'
// or
import 'tippy.js/dist/tippy.css' // optional for styling


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .directive('debounce', (el, binding) => debounce(el, binding))
            .directive('click-outside', clickOutside)
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
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
