import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { createSSRApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import SeoHead from '@/Components/Seo/SeoHead.vue';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => title,
        resolve: (name) => {
            const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
            const pageModule = pages[`./Pages/${name}.vue`];
            const component = pageModule.default;

            if (!component.__seoLayoutApplied) {
                const existingLayout = component.layout;

                component.layout = (layoutH, child) => {
                    const content = existingLayout ? existingLayout(layoutH, child) : child;

                    return [layoutH(SeoHead), content];
                };

                component.__seoLayoutApplied = true;
            }

            return pageModule;
        },
        setup({ App, props, plugin }) {
            const pinia = createPinia();

            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(pinia)
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                });
        },
    }),
);
