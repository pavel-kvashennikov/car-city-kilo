<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Car, Shield } from 'lucide-vue-next'

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

const isActive = (href) => href === '/admin' ? currentUrl.value === '/admin' : currentUrl.value.startsWith(href)
</script>

<template>
    <div class="min-h-screen">
        <header class="bg-carbon-deep text-white sticky top-0 z-40 shadow-elevated">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <Link href="/" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                            <Car class="w-4 h-4 text-white" />
                        </div>
                        <span class="font-bold">AutoMarket</span>
                    </Link>
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-red-500/20 text-red-300 font-semibold border border-red-500/30">
                        <Shield class="w-3 h-3" />
                        Админ
                    </span>
                </div>
                <span class="text-sm text-gray-400">{{ user?.name }}</span>
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
