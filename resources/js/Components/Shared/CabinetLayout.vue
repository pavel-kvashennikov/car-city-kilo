<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Car, LayoutDashboard, Building2, Package, ClipboardList, Wrench,
    Users, Calendar, CalendarCheck, UserCog, CreditCard, BarChart2,
    Menu, LogOut, ChevronRight
} from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const company = computed(() => page.props.company);
const currentUrl = computed(() => page.url);
const profiles = computed(() => {
    const shared = page.props.activeProfiles;
    if (Array.isArray(shared)) return shared;
    return (page.props.company?.active_profiles ?? []).map(p => (typeof p === 'object' ? p.type : p));
});

const hasProfile = (type) => profiles.value.length === 0 || profiles.value.includes(type);

const navigation = computed(() => [
    { name: 'Дашборд', href: '/cabinet', icon: LayoutDashboard, always: true },
    { name: 'Профиль компании', href: '/cabinet/company', icon: Building2, always: true },
    { name: 'Автомобили', href: '/cabinet/dealer/vehicles', icon: Car, show: hasProfile('dealer') },
    { name: 'Лиды', href: '/cabinet/dealer/leads', icon: ClipboardList, show: hasProfile('dealer') },
    { name: 'Запчасти', href: '/cabinet/parts/products', icon: Package, show: hasProfile('parts') },
    { name: 'Заказы', href: '/cabinet/parts/orders', icon: ClipboardList, show: hasProfile('parts') },
    { name: 'Услуги', href: '/cabinet/service/items', icon: Wrench, show: hasProfile('service') },
    { name: 'Мастера', href: '/cabinet/service/masters', icon: Users, show: hasProfile('service') },
    { name: 'Расписание', href: '/cabinet/service/schedule', icon: Calendar, show: hasProfile('service') },
    { name: 'Записи', href: '/cabinet/service/appointments', icon: CalendarCheck, show: hasProfile('service') },
    { name: 'Сотрудники', href: '/cabinet/staff', icon: UserCog, always: true },
    { name: 'Биллинг', href: '/cabinet/billing', icon: CreditCard, always: true },
    { name: 'Аналитика', href: '/cabinet/analytics', icon: BarChart2, always: true },
].filter(item => item.always || item.show));

const isActive = (href) => currentUrl.value === href || (href !== '/cabinet' && currentUrl.value.startsWith(href));
</script>

<template>
    <div class="min-h-screen bg-surface">
        <header class="header-glass sticky top-0 z-40">
            <div class="container-app flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <Link href="/" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shadow-sm">
                            <Car class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="font-bold text-gradient hidden sm:inline">AutoMarket</span>
                    </Link>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-primary-light text-primary font-bold">Кабинет</span>
                    <span v-if="company" class="hidden md:inline text-on-surface-muted text-sm border-l border-outline pl-3 ml-1">{{ company.name }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="font-medium text-on-surface hidden sm:inline">{{ user?.name }}</span>
                    <Link href="/logout" method="post" as="button" class="btn-ghost !p-2 text-danger/70 hover:text-danger">
                        <LogOut style="width:1rem;height:1rem" />
                    </Link>
                </div>
            </div>
        </header>

        <div class="container-app py-6 flex gap-6">
            <!-- Sidebar -->
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
                        <span class="truncate">{{ item.name }}</span>
                    </Link>

                    <div class="pt-3 mt-2 border-t border-outline">
                        <Link
                            href="/"
                            class="sidebar-link text-on-surface-muted/70 hover:text-on-surface hover:bg-surface-muted"
                        >
                            <ChevronRight class="w-4 h-4 rotate-180" />
                            На главную
                        </Link>
                    </div>
                </div>
            </aside>

            <!-- Main content -->
            <main class="flex-1 min-w-0">
                <slot />
            </main>
        </div>
    </div>
</template>
