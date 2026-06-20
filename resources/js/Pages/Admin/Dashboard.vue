<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Building2, Users, AlertCircle, ShoppingCart, Car, Package,
    Wrench, TrendingUp, CalendarCheck, Newspaper, CheckCircle
} from 'lucide-vue-next';

const props = defineProps({
    stats:           { type: Object, default: () => ({}) },
    recentCompanies: { type: Array,  default: () => [] },
    recentOrders:    { type: Array,  default: () => [] },
});

const kpis = [
    { label: 'Всего компаний',     key: 'companies',          icon: Building2,     color: 'text-blue-500',    bg: 'bg-blue-50' },
    { label: 'Пользователей',      key: 'users',              icon: Users,         color: 'text-violet-500',  bg: 'bg-violet-50' },
    { label: 'На модерации',       key: 'pending',            icon: AlertCircle,   color: 'text-amber-500',   bg: 'bg-amber-50' },
    { label: 'Заказов сегодня',    key: 'orders_today',       icon: ShoppingCart,  color: 'text-teal-500',    bg: 'bg-teal-50' },
    { label: 'Активных авто',      key: 'vehicles',           icon: Car,           color: 'text-indigo-500',  bg: 'bg-indigo-50' },
    { label: 'Запчастей',          key: 'products',           icon: Package,       color: 'text-cyan-500',    bg: 'bg-cyan-50' },
    { label: 'Сервисов',           key: 'services',           icon: Wrench,        color: 'text-rose-500',    bg: 'bg-rose-50' },
    { label: 'Записей за месяц',   key: 'appointments_month', icon: CalendarCheck, color: 'text-pink-500',    bg: 'bg-pink-50' },
    { label: 'Заказов за месяц',   key: 'orders_month',       icon: TrendingUp,    color: 'text-emerald-500', bg: 'bg-emerald-50' },
    { label: 'Выручка за месяц',   key: 'revenue_month',      icon: TrendingUp,    color: 'text-green-600',   bg: 'bg-green-50', suffix: ' ₽' },
    { label: 'Статей в блоге',     key: 'blog_posts',         icon: Newspaper,     color: 'text-orange-500',  bg: 'bg-orange-50' },
    { label: 'Активных компаний',  key: 'companies_active',   icon: CheckCircle,   color: 'text-blue-600',    bg: 'bg-blue-50' },
];

const STATUS_COMPANY = {
    active:   { label: 'Активна',    cls: 'badge-success' },
    pending:  { label: 'На модерации', cls: 'badge-warning' },
    rejected: { label: 'Отклонена',  cls: 'badge-danger' },
    suspended:{ label: 'Приостановлена', cls: 'badge-neutral' },
};

const STATUS_ORDER = {
    pending:    { label: 'Ожидает',    cls: 'badge-warning' },
    confirmed:  { label: 'Подтверждён', cls: 'badge-info' },
    processing: { label: 'В обработке', cls: 'badge-primary' },
    shipped:    { label: 'Отправлен',  cls: 'badge-info' },
    delivered:  { label: 'Доставлен',  cls: 'badge-success' },
    cancelled:  { label: 'Отменён',   cls: 'badge-danger' },
};

const fmt = (n) => n?.toLocaleString('ru-RU') ?? 0;
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="page-title">Панель администратора</h1>
                <p class="page-subtitle">Обзор платформы «Город машин»</p>
            </div>

            <!-- KPI grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <div v-for="kpi in kpis" :key="kpi.key" class="card p-4 flex items-center gap-3">
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', kpi.bg]">
                        <component :is="kpi.icon" :class="['w-5 h-5', kpi.color]" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-on-surface">
                            {{ fmt(stats[kpi.key]) }}{{ kpi.suffix ?? '' }}
                        </p>
                        <p class="text-xs text-on-surface-muted truncate">{{ kpi.label }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent companies -->
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-outline">
                        <h2 class="font-semibold text-sm text-on-surface">Новые компании</h2>
                        <Link href="/admin/companies" class="text-xs text-primary hover:underline">Все компании →</Link>
                    </div>
                    <div v-if="!recentCompanies.length" class="p-8 text-center text-sm text-on-surface-muted">
                        Нет данных
                    </div>
                    <div class="divide-y divide-outline">
                        <div v-for="co in recentCompanies" :key="co.id"
                            class="flex items-center gap-3 px-5 py-3">
                            <div class="w-8 h-8 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                <Building2 class="w-4 h-4 text-on-surface-muted" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-on-surface truncate">{{ co.name }}</p>
                                <p class="text-xs text-on-surface-muted">{{ fmtDate(co.created_at) }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0"
                                :class="STATUS_COMPANY[co.status]?.cls ?? 'badge-neutral'">
                                {{ STATUS_COMPANY[co.status]?.label ?? co.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Recent orders -->
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-outline">
                        <h2 class="font-semibold text-sm text-on-surface">Последние заказы</h2>
                        <span class="text-xs text-on-surface-muted">за сегодня: {{ stats.orders_today ?? 0 }}</span>
                    </div>
                    <div v-if="!recentOrders.length" class="p-8 text-center text-sm text-on-surface-muted">
                        Заказов пока нет
                    </div>
                    <div class="divide-y divide-outline">
                        <div v-for="order in recentOrders" :key="order.id"
                            class="flex items-center gap-3 px-5 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-on-surface">
                                    {{ order.order_number ?? `#${order.id}` }}
                                </p>
                                <p class="text-xs text-on-surface-muted">
                                    {{ order.user?.name ?? '—' }} · {{ fmtDate(order.created_at) }}
                                </p>
                            </div>
                            <p class="text-sm font-semibold text-on-surface shrink-0">
                                {{ fmt(order.total_amount) }} ₽
                            </p>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0"
                                :class="STATUS_ORDER[order.status]?.cls ?? 'badge-neutral'">
                                {{ STATUS_ORDER[order.status]?.label ?? order.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick links -->
            <div class="card p-5">
                <h2 class="font-semibold text-sm text-on-surface mb-4 border-b border-outline pb-3">Быстрые действия</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <Link v-for="link in [
                        { label: 'Компании',       href: '/admin/companies',    icon: Building2 },
                        { label: 'Модерация',      href: '/admin/moderation',   icon: AlertCircle },
                        { label: 'Блог',           href: '/admin/blog/posts',   icon: Newspaper },
                        { label: 'Биллинг',        href: '/admin/billing',      icon: TrendingUp },
                    ]" :key="link.href" :href="link.href"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl border border-outline hover:border-primary/40 hover:bg-primary/5 transition-colors text-center">
                        <component :is="link.icon" class="w-6 h-6 text-primary" />
                        <span class="text-xs font-semibold text-on-surface">{{ link.label }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
