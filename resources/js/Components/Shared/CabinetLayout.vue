<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Car, LayoutDashboard } from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const company = computed(() => page.props.company)
const currentUrl = computed(() => page.url)

const navigation = [
    { name: 'Дашборд', href: '/cabinet', icon: LayoutDashboard },
    { name: 'Компания', href: '/cabinet/company' },
    { name: 'Автомобили', href: '/cabinet/dealer/vehicles', profile: 'dealer' },
    { name: 'Лиды', href: '/cabinet/dealer/leads', profile: 'dealer' },
    { name: 'Запчасти', href: '/cabinet/parts/products', profile: 'parts' },
    { name: 'Заказы', href: '/cabinet/parts/orders', profile: 'parts' },
    { name: 'Услуги', href: '/cabinet/service/items', profile: 'service' },
    { name: 'Мастера', href: '/cabinet/service/masters', profile: 'service' },
    { name: 'Расписание', href: '/cabinet/service/schedule', profile: 'service' },
    { name: 'Записи', href: '/cabinet/service/appointments', profile: 'service' },
    { name: 'Сотрудники', href: '/cabinet/staff' },
    { name: 'Биллинг', href: '/cabinet/billing' },
    { name: 'Аналитика', href: '/cabinet/analytics' },
]

const isActive = (href) => currentUrl.value === href || (href !== '/cabinet' && currentUrl.value.startsWith(href))
</script>

<template>
    <div class="min-h-screen">
        <header class="header-glass sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                        <Car class="w-4 h-4 text-white" />
                    </div>
                    <span class="font-bold text-gradient">AutoMarket</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-primary-light text-primary font-semibold ml-1">Кабинет</span>
                </Link>
                <div class="flex items-center gap-4 text-sm">
                    <span v-if="company" class="text-on-surface-muted hidden sm:inline">{{ company.name }}</span>
                    <span class="font-medium text-on-surface">{{ user?.name }}</span>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex gap-6">
            <aside class="w-60 shrink-0 hidden md:block">
                <nav class="surface-panel p-3 space-y-0.5 sticky top-20">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="sidebar-link"
                        :class="{ 'sidebar-link-active': isActive(item.href) }"
                    >
                        {{ item.name }}
                    </Link>
                </nav>
            </aside>

            <main class="flex-1 min-w-0">
                <slot />
            </main>
        </div>
    </div>
</template>
