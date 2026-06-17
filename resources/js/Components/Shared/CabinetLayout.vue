<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const company = computed(() => page.props.company)
const currentUrl = computed(() => page.url)

const navigation = [
    { name: 'Дашборд', href: '/cabinet' },
    { name: 'Компания', href: '/cabinet/company' },
    { name: 'Автомобили', href: '/cabinet/dealer/vehicles', profile: 'dealer' },
    { name: 'Лиды', href: '/cabinet/dealer/leads', profile: 'dealer' },
    { name: 'Запчасти', href: '/cabinet/parts/products', profile: 'parts' },
    { name: 'Заказы', href: '/cabinet/parts/orders', profile: 'parts' },
    { name: 'Услуги', href: '/cabinet/service/service-items', profile: 'service' },
    { name: 'Мастера', href: '/cabinet/service/masters', profile: 'service' },
    { name: 'Записи', href: '/cabinet/service/appointments', profile: 'service' },
    { name: 'Сотрудники', href: '/cabinet/staff' },
    { name: 'Биллинг', href: '/cabinet/billing' },
    { name: 'Аналитика', href: '/cabinet/analytics' },
]

const isActive = (href) => currentUrl.value.startsWith(href)
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="bg-white border-b sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
                <Link href="/" class="text-lg font-bold text-blue-600">AutoMarket</Link>
                <div class="flex items-center gap-4">
                    <span v-if="company" class="text-sm text-gray-500">{{ company.name }}</span>
                    <span class="text-sm font-medium">{{ user?.name }}</span>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 py-6 flex gap-6">
            <aside class="w-56 shrink-0">
                <nav class="space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="block px-3 py-2 rounded-lg text-sm transition"
                        :class="isActive(item.href) ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100'"
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
