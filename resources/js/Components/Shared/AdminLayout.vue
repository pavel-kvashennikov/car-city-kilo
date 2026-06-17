<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const currentUrl = computed(() => page.url)

const navigation = [
    { name: 'Дашборд', href: '/admin' },
    { name: 'Компании', href: '/admin/companies' },
    { name: 'Модерация', href: '/admin/moderation' },
    { name: 'Карта рынка', href: '/admin/market-map' },
    { name: 'Каталог', href: '/admin/catalog' },
    { name: 'Биллинг', href: '/admin/billing' },
    { name: 'Аналитика', href: '/admin/analytics' },
]

const isActive = (href) => currentUrl.value.startsWith(href)
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-gray-900 text-white sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <Link href="/" class="text-lg font-bold">AutoMarket</Link>
                    <span class="text-xs bg-red-600 px-2 py-0.5 rounded">Админ</span>
                </div>
                <span class="text-sm text-gray-300">{{ user?.name }}</span>
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
                        :class="isActive(item.href) ? 'bg-white shadow text-gray-900 font-medium' : 'text-gray-600 hover:bg-white hover:shadow-sm'"
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
