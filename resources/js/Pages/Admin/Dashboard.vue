<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Building2, Users, AlertCircle, ShoppingCart, Car, Package, Wrench, TrendingUp } from 'lucide-vue-next';

defineProps({ stats: { type: Object, default: () => ({}) } });

const statCards = [
    { label: 'Компании', key: 'companies', icon: Building2, color: 'from-blue-500 to-blue-700' },
    { label: 'Пользователи', key: 'users', icon: Users, color: 'from-violet-500 to-violet-700' },
    { label: 'На модерации', key: 'pending', icon: AlertCircle, color: 'from-amber-500 to-orange-600' },
    { label: 'Заказы сегодня', key: 'orders_today', icon: ShoppingCart, color: 'from-teal-500 to-teal-700' },
    { label: 'Автомобилей', key: 'vehicles', icon: Car, color: 'from-indigo-500 to-indigo-700' },
    { label: 'Запчастей', key: 'products', icon: Package, color: 'from-cyan-500 to-cyan-700' },
    { label: 'Сервисов', key: 'services', icon: Wrench, color: 'from-rose-500 to-rose-700' },
    { label: 'Выручка за месяц', key: 'revenue_month', icon: TrendingUp, color: 'from-emerald-500 to-emerald-700', prefix: '', suffix: ' ₽' },
];
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="page-title">Панель администратора</h1>
                <p class="page-subtitle">Обзор платформы AutoMarket</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div
                    v-for="s in statCards"
                    :key="s.key"
                    class="card p-5 flex items-center gap-4"
                >
                    <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center shrink-0', s.color]">
                        <component :is="s.icon" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-on-surface">
                            {{ s.prefix || '' }}{{ stats[s.key] ?? 0 }}{{ s.suffix || '' }}
                        </p>
                        <p class="text-xs text-on-surface-muted mt-0.5">{{ s.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick admin actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="card p-5">
                    <h2 class="font-bold text-sm text-on-surface mb-4">Быстрые действия</h2>
                    <div class="space-y-2">
                        <Link
                            v-for="(link, i) in [
                                { label: 'Список компаний', href: '/admin/companies', icon: Building2 },
                                { label: 'Заявки на модерацию', href: '/admin/moderation', icon: AlertCircle },
                                { label: 'Карта рынка', href: '/admin/market-map', icon: null },
                                { label: 'Биллинг', href: '/admin/billing', icon: null },
                            ]"
                            :key="i"
                            :href="link.href"
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-muted transition-colors text-sm font-medium text-on-surface hover:text-primary"
                        >
                            <component v-if="link.icon" :is="link.icon" class="w-4 h-4 text-primary" />
                            {{ link.label }}
                        </Link>
                    </div>
                </div>

                <div class="card p-5 bg-gradient-to-br from-carbon-deep to-carbon text-white">
                    <h2 class="font-bold text-sm mb-4 text-gray-300">Информация о системе</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-white/10">
                            <span class="text-gray-400">Версия</span>
                            <span class="font-mono">v1.0.0</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-white/10">
                            <span class="text-gray-400">Активных компаний</span>
                            <span>{{ stats.companies ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-400">Статус системы</span>
                            <span class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" /> Активна
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
