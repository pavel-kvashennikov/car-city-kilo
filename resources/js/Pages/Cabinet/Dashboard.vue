<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Eye, ClipboardList, ShoppingCart, Car, Package, Wrench, ArrowRight, TrendingUp } from 'lucide-vue-next';

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    company: { type: Object, default: () => null },
});

const statCards = [
    { label: 'Просмотры сегодня', key: 'views_today', icon: Eye, color: 'from-blue-500 to-blue-700' },
    { label: 'Лиды за неделю', key: 'leads_week', icon: ClipboardList, color: 'from-violet-500 to-violet-700' },
    { label: 'Заказы за месяц', key: 'orders_month', icon: ShoppingCart, color: 'from-teal-500 to-teal-700' },
    { label: 'Рост показов', key: 'impressions_growth', icon: TrendingUp, color: 'from-amber-500 to-orange-600', suffix: '%' },
];

const quickLinks = [
    { label: 'Добавить автомобиль', href: '/cabinet/dealer/vehicles/create', icon: Car },
    { label: 'Добавить запчасть', href: '/cabinet/parts/products/create', icon: Package },
    { label: 'Мастера сервиса', href: '/cabinet/service/masters', icon: Wrench },
    { label: 'Аналитика', href: '/cabinet/analytics', icon: TrendingUp },
];
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6">
            <!-- Page title -->
            <div>
                <h1 class="page-title">Дашборд</h1>
                <p class="page-subtitle">{{ company?.name || 'Ваш личный кабинет' }}</p>
            </div>

            <!-- Stats row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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
                            {{ stats[s.key] ?? 0 }}{{ s.suffix || '' }}
                        </p>
                        <p class="text-xs text-on-surface-muted mt-0.5">{{ s.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Company status & quick links -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Company status -->
                <div class="card p-5">
                    <h2 class="font-bold text-sm text-on-surface mb-4">Статус компании</h2>
                    <div v-if="company" class="space-y-3">
                        <div class="flex items-center justify-between py-2.5 border-b border-outline text-sm">
                            <span class="text-on-surface-muted">Название</span>
                            <span class="font-semibold">{{ company.name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 border-b border-outline text-sm">
                            <span class="text-on-surface-muted">Статус</span>
                            <span
                                class="badge"
                                :class="{
                                    'badge-success': company.status === 'active',
                                    'badge-warning': company.status === 'pending',
                                    'badge-danger': company.status === 'suspended',
                                    'badge-neutral': !company.status,
                                }"
                            >
                                {{ company.status === 'active' ? 'Активна' : company.status === 'pending' ? 'На проверке' : company.status ?? '—' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 text-sm">
                            <span class="text-on-surface-muted">Профили</span>
                            <div class="flex gap-1.5">
                                <span v-for="p in (company.active_profiles ?? [])" :key="p.type" class="badge badge-primary">{{ p.type }}</span>
                                <span v-if="!company.active_profiles?.length" class="text-on-surface-muted">—</span>
                            </div>
                        </div>
                        <Link href="/cabinet/company" class="btn-secondary !text-xs !py-1.5 w-full !justify-center mt-2">
                            Редактировать профиль <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                    <div v-else class="text-center py-6">
                        <p class="text-sm text-on-surface-muted mb-3">Компания ещё не создана</p>
                        <Link href="/cabinet/company" class="btn-primary !text-sm">Создать компанию</Link>
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="card p-5">
                    <h2 class="font-bold text-sm text-on-surface mb-4">Быстрые действия</h2>
                    <div class="grid grid-cols-2 gap-2.5">
                        <Link
                            v-for="link in quickLinks"
                            :key="link.href"
                            :href="link.href"
                            class="flex flex-col items-center gap-2 p-4 rounded-xl border border-outline hover:border-primary-bright hover:bg-primary-light transition-all group text-center"
                        >
                            <component :is="link.icon" class="w-5 h-5 text-primary group-hover:scale-110 transition-transform" />
                            <span class="text-xs font-semibold text-on-surface group-hover:text-primary leading-tight">{{ link.label }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </CabinetLayout>
</template>
