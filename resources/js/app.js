import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import SeoHead from '@/Components/Seo/SeoHead.vue';

createInertiaApp({
    title: (title) => title,
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        );

        const component = page.default;

        if (!component.__seoLayoutApplied) {
            const existingLayout = component.layout;

            component.layout = (layoutH, child) => {
                const content = existingLayout ? existingLayout(layoutH, child) : child;

                return [layoutH(SeoHead), content];
            };

            component.__seoLayoutApplied = true;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
