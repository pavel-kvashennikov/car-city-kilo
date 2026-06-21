<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Car, Shield, LayoutDashboard, Building2, AlertCircle, MapPin,
    BookOpen, BarChart2, LogOut, Newspaper
} from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentUrl = computed(() => page.url);

const navigation = [
    { name: 'Дашборд', href: '/admin', icon: LayoutDashboard },
    { name: 'Компании', href: '/admin/companies', icon: Building2 },
    { name: 'Модерация', href: '/admin/moderation', icon: AlertCircle },
    { name: 'Карта рынка', href: '/admin/market-map', icon: MapPin },
    { name: 'Каталог', href: '/admin/catalog', icon: BookOpen },
    { name: 'Аналитика', href: '/admin/analytics', icon: BarChart2 },
    { name: 'Блог', href: '/admin/blog/posts', icon: Newspaper },
];

const isActive = (href) => href === '/admin' ? currentUrl.value === '/admin' : currentUrl.value.startsWith(href);
</script>

<template>
    <div class="min-h-screen bg-surface">
        <header class="bg-carbon-deep text-white sticky top-0 z-40">
            <div class="h-px bg-gradient-to-r from-red-500/40 via-red-400/60 to-red-500/40" />
            <div class="container-app flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <Link href="/" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                            <Car class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="font-bold hidden sm:inline">Город машин</span>
                    </Link>
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-red-500/20 text-red-300 font-bold border border-red-500/30">
                        <Shield class="w-3 h-3" /> Администратор
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400 hidden sm:inline">{{ user?.name }}</span>
                    <Link href="/logout" method="post" as="button" class="p-2 text-gray-500 hover:text-red-400 transition-colors">
                        <LogOut class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </header>

        <div class="container-app py-6 flex gap-6">
            <aside class="w-56 shrink-0 hidden md:block">
                <div class="card p-3 space-y-0.5 sticky top-20">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="sidebar-link"
                        :class="{ 'sidebar-link-active': isActive(item.href) }"
                    >
                        <component :is="item.icon" class="w-4 h-4 shrink-0" />
                        {{ item.name }}
                    </Link>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                <slot />
            </main>
        </div>
    </div>
</template>
