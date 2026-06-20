<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import { computed } from 'vue';
import {
    Car, ClipboardList, ShoppingCart, CalendarCheck,
    TrendingUp, Package, Wrench, AlertCircle,
    CheckCircle, BarChart2
} from 'lucide-vue-next';

const props = defineProps({
    dealer:  { type: Object, default: null },
    parts:   { type: Object, default: null },
    service: { type: Object, default: null },
    days:    { type: Array,  default: () => [] },
});

const hasAny = computed(() => props.dealer || props.parts || props.service);

// ─── Mini bar chart ────────────────────────────────────────────────────────────
function buildChart(dailyMap) {
    const bars = props.days.map(d => ({ date: d, val: dailyMap?.[d] ?? 0 }));
    const max = Math.max(...bars.map(b => b.val), 1);
    return bars.map(b => ({ ...b, pct: Math.round((b.val / max) * 100) }));
}

const dealerChart  = computed(() => buildChart(props.dealer?.daily_leads));
const serviceChart = computed(() => buildChart(props.service?.daily_appts));

function fmtDay(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' });
}

function fmt(n) {
    return n != null ? Number(n).toLocaleString('ru-RU') + ' ₽' : '—';
}

const leadTypeLabels = {
    test_drive: 'Тест-драйв',
    credit:     'Кредит',
    trade_in:   'Trade-in',
    callback:   'Звонок',
};
const apptStatusLabels = {
    pending:   'Ожидают',
    confirmed: 'Подтверждены',
    completed: 'Выполнены',
    cancelled: 'Отменены',
};
const apptStatusCls = {
    pending:   'bg-amber-400',
    confirmed: 'bg-primary',
    completed: 'bg-tertiary',
    cancelled: 'bg-outline',
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-8">

            <div>
                <h1 class="page-title">Аналитика</h1>
                <p class="page-subtitle">Показатели вашей компании за последние 30 дней</p>
            </div>

            <!-- No profiles -->
            <div v-if="!hasAny" class="card p-16 text-center">
                <BarChart2 class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="font-semibold text-on-surface">Нет данных для отображения</p>
                <p class="text-sm text-on-surface-muted mt-1">Настройте профиль компании и добавьте объявления</p>
            </div>

            <!-- ── DEALER ─────────────────────────────────────────────────── -->
            <section v-if="dealer" class="space-y-5">
                <div class="flex items-center gap-2 border-b border-outline pb-3">
                    <Car class="w-5 h-5 text-primary" />
                    <h2 class="font-bold text-on-surface">Автосалон</h2>
                </div>

                <!-- KPI cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Автомобилей</p>
                        <p class="text-3xl font-extrabold text-on-surface">{{ dealer.vehicles_active }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">из {{ dealer.vehicles_total }} всего</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Лидов за 30 дней</p>
                        <p class="text-3xl font-extrabold text-on-surface">{{ dealer.leads_this_month }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">{{ dealer.leads_total }} всего</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Новых лидов</p>
                        <p class="text-3xl font-extrabold text-on-surface" :class="dealer.leads_new > 0 ? 'text-warning' : ''">
                            {{ dealer.leads_new }}
                        </p>
                        <p class="text-xs text-on-surface-muted mt-0.5">ожидают обработки</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Лидов / авто</p>
                        <p class="text-3xl font-extrabold text-on-surface">{{ dealer.conversion }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">среднее значение</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    <!-- Leads chart last 14 days -->
                    <div class="card p-5">
                        <p class="font-semibold text-on-surface mb-4">Лиды за 14 дней</p>
                        <div class="flex items-end gap-1 h-28">
                            <div v-for="bar in dealerChart" :key="bar.date"
                                class="flex-1 flex flex-col items-center gap-1 group">
                                <span class="text-xs text-on-surface-muted opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ bar.val || '' }}
                                </span>
                                <div class="w-full rounded-t-sm transition-all"
                                    :style="{ height: bar.pct + '%', minHeight: bar.val > 0 ? '4px' : '0' }"
                                    :class="bar.val > 0 ? 'bg-primary' : 'bg-surface-dim'" />
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-xs text-on-surface-muted">{{ fmtDay(days[0]) }}</span>
                            <span class="text-xs text-on-surface-muted">{{ fmtDay(days[days.length - 1]) }}</span>
                        </div>
                    </div>

                    <!-- Leads by type -->
                    <div class="card p-5">
                        <p class="font-semibold text-on-surface mb-4">Типы заявок</p>
                        <div v-if="Object.keys(dealer.leads_by_type || {}).length" class="space-y-3">
                            <div v-for="(cnt, type) in dealer.leads_by_type" :key="type">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-on-surface">{{ leadTypeLabels[type] ?? type }}</span>
                                    <span class="font-semibold text-on-surface">{{ cnt }}</span>
                                </div>
                                <div class="h-1.5 bg-surface-muted rounded-full">
                                    <div class="h-full bg-primary rounded-full transition-all"
                                        :style="{ width: Math.round(cnt / dealer.leads_total * 100) + '%' }" />
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-on-surface-muted">Нет данных</p>
                    </div>

                    <!-- Top vehicles -->
                    <div v-if="dealer.top_vehicles?.length" class="card p-5 lg:col-span-2">
                        <p class="font-semibold text-on-surface mb-4">Топ авто по заявкам (30 дней)</p>
                        <div class="space-y-2">
                            <div v-for="(v, i) in dealer.top_vehicles" :key="i"
                                class="flex items-center gap-3 py-2 border-b border-outline last:border-0">
                                <span class="text-xs font-bold text-on-surface-muted w-5 text-center">{{ i + 1 }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-on-surface truncate">{{ v.title || 'Без названия' }}</p>
                                    <p v-if="v.price" class="text-xs text-on-surface-muted">{{ fmt(v.price) }}</p>
                                </div>
                                <span class="text-sm font-bold text-primary shrink-0">{{ v.leads }} лид.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── PARTS ──────────────────────────────────────────────────── -->
            <section v-if="parts" class="space-y-5">
                <div class="flex items-center gap-2 border-b border-outline pb-3">
                    <Package class="w-5 h-5 text-tertiary" />
                    <h2 class="font-bold text-on-surface">Запчасти</h2>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Товаров</p>
                        <p class="text-3xl font-extrabold text-on-surface">{{ parts.products_active }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">из {{ parts.products_total }} всего</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Заказов за 30 дней</p>
                        <p class="text-3xl font-extrabold text-on-surface">{{ parts.orders_this_month }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">{{ parts.orders_total }} всего</p>
                    </div>
                </div>
            </section>

            <!-- ── SERVICE ────────────────────────────────────────────────── -->
            <section v-if="service" class="space-y-5">
                <div class="flex items-center gap-2 border-b border-outline pb-3">
                    <Wrench class="w-5 h-5 text-accent" />
                    <h2 class="font-bold text-on-surface">Автосервис</h2>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Записей за 30 дней</p>
                        <p class="text-3xl font-extrabold text-on-surface">{{ service.appt_this_month }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">{{ service.appt_total }} всего</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-1">Ожидают</p>
                        <p class="text-3xl font-extrabold text-on-surface"
                            :class="service.appt_new > 0 ? 'text-warning' : ''">
                            {{ service.appt_new }}
                        </p>
                        <p class="text-xs text-on-surface-muted mt-0.5">новых записей</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    <!-- Appointments chart -->
                    <div class="card p-5">
                        <p class="font-semibold text-on-surface mb-4">Записи за 14 дней</p>
                        <div class="flex items-end gap-1 h-28">
                            <div v-for="bar in serviceChart" :key="bar.date"
                                class="flex-1 flex flex-col items-center gap-1 group">
                                <span class="text-xs text-on-surface-muted opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ bar.val || '' }}
                                </span>
                                <div class="w-full rounded-t-sm transition-all"
                                    :style="{ height: bar.pct + '%', minHeight: bar.val > 0 ? '4px' : '0' }"
                                    :class="bar.val > 0 ? 'bg-accent' : 'bg-surface-dim'" />
                            </div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-xs text-on-surface-muted">{{ fmtDay(days[0]) }}</span>
                            <span class="text-xs text-on-surface-muted">{{ fmtDay(days[days.length - 1]) }}</span>
                        </div>
                    </div>

                    <!-- Appointments by status -->
                    <div class="card p-5">
                        <p class="font-semibold text-on-surface mb-4">Записи по статусам</p>
                        <div v-if="Object.keys(service.appt_by_status || {}).length" class="space-y-3">
                            <div v-for="(cnt, status) in service.appt_by_status" :key="status">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full"
                                            :class="apptStatusCls[status] ?? 'bg-outline'" />
                                        <span class="text-on-surface">{{ apptStatusLabels[status] ?? status }}</span>
                                    </div>
                                    <span class="font-semibold text-on-surface">{{ cnt }}</span>
                                </div>
                                <div class="h-1.5 bg-surface-muted rounded-full">
                                    <div class="h-full rounded-full transition-all"
                                        :class="apptStatusCls[status] ?? 'bg-outline'"
                                        :style="{ width: Math.round(cnt / service.appt_total * 100) + '%' }" />
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-on-surface-muted">Нет данных</p>
                    </div>
                </div>
            </section>

        </div>
    </CabinetLayout>
</template>
