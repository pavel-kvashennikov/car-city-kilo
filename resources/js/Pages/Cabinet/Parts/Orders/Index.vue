<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Package, Phone, Mail, MapPin, MessageSquare,
    ChevronDown, Inbox, CheckCircle, Clock, Truck, XCircle, AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    orders:  { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
});

// ─── Status config ────────────────────────────────────────────────────────────
const statuses = [
    { value: '',           label: 'Все' },
    { value: 'pending',    label: 'Новые',       icon: AlertCircle },
    { value: 'confirmed',  label: 'Подтверждён', icon: CheckCircle },
    { value: 'processing', label: 'В обработке', icon: Clock },
    { value: 'ready',      label: 'Готов',       icon: Package },
    { value: 'completed',  label: 'Выдан',       icon: Truck },
    { value: 'cancelled',  label: 'Отменён',     icon: XCircle },
];

const statusCfg = {
    pending:    { label: 'Новый',        cls: 'badge-warning' },
    confirmed:  { label: 'Подтверждён', cls: 'badge-primary' },
    processing: { label: 'В обработке', cls: 'badge-primary' },
    ready:      { label: 'Готов к выдаче', cls: 'badge-success' },
    completed:  { label: 'Выдан',       cls: 'badge-neutral' },
    cancelled:  { label: 'Отменён',     cls: 'badge-neutral' },
};

// Status transitions
const nextStatuses = {
    pending:    [{ value: 'confirmed',  label: 'Подтвердить' }, { value: 'cancelled', label: 'Отменить', danger: true }],
    confirmed:  [{ value: 'processing', label: 'В обработку' }, { value: 'cancelled', label: 'Отменить', danger: true }],
    processing: [{ value: 'ready',      label: 'Готов к выдаче' }, { value: 'cancelled', label: 'Отменить', danger: true }],
    ready:      [{ value: 'completed',  label: 'Отметить выданным' }],
};

// ─── Filtering ────────────────────────────────────────────────────────────────
const activeStatus = ref(props.filters.status ?? '');

function applyFilter(status) {
    activeStatus.value = status;
    router.get('/cabinet/parts/orders', { status: status || undefined },
        { preserveScroll: true, replace: true });
}

// ─── Actions ──────────────────────────────────────────────────────────────────
const expandedNotes = ref({});

function setStatus(order, status) {
    router.put(`/cabinet/parts/orders/${order.id}`, { status }, { preserveScroll: true });
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function fmtDate(v) {
    if (!v) return '—';
    return new Date(v).toLocaleString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function fmt(n) {
    return n != null ? Number(n).toLocaleString('ru-RU') + ' ₽' : '—';
}

function getStatusVal(order) {
    return order.status?.value ?? order.status ?? 'pending';
}

function deliveryLabel(method) {
    return { pickup: 'Самовывоз', delivery: 'Доставка', courier: 'Курьер' }[method] ?? method ?? '—';
}
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div>
                <h1 class="page-title">Заказы запчастей</h1>
                <p class="page-subtitle">Входящие заказы от покупателей вашего магазина</p>
            </div>

            <!-- Status tabs -->
            <div class="flex items-center gap-1 flex-wrap">
                <button
                    v-for="st in statuses" :key="st.value"
                    @click="applyFilter(st.value)"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors"
                    :class="activeStatus === st.value
                        ? 'bg-primary text-white'
                        : 'bg-surface-muted text-on-surface-muted hover:bg-surface-dim'"
                >
                    <component :is="st.icon" v-if="st.icon" class="w-3.5 h-3.5" />
                    {{ st.label }}
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="!orders.data?.length" class="card p-16 text-center">
                <Inbox class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">Заказов пока нет</p>
                <p class="text-sm text-on-surface-muted mt-1">
                    Они появятся когда покупатели оформят заказ на ваши товары
                </p>
            </div>

            <!-- Order cards -->
            <div v-else class="space-y-4">
                <div v-for="order in orders.data" :key="order.id" class="card overflow-hidden">

                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 p-5 pb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="font-mono font-bold text-on-surface">
                                    #{{ order.order_number }}
                                </span>
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                    :class="statusCfg[getStatusVal(order)]?.cls ?? 'badge-neutral'">
                                    {{ statusCfg[getStatusVal(order)]?.label ?? getStatusVal(order) }}
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-muted">{{ fmtDate(order.created_at) }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-lg text-on-surface">{{ fmt(order.total_amount) }}</p>
                            <p class="text-xs text-on-surface-muted">{{ order.items?.length ?? 0 }} позиц.</p>
                        </div>
                    </div>

                    <div class="px-5 pb-4 grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Client info -->
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-2">Покупатель</p>
                            <p class="font-semibold text-on-surface">
                                {{ order.client_name ?? order.user?.name ?? 'Не указан' }}
                            </p>
                            <a v-if="order.client_phone" :href="`tel:${order.client_phone}`"
                                class="flex items-center gap-2 text-sm text-primary hover:underline">
                                <Phone class="w-3.5 h-3.5" /> {{ order.client_phone }}
                            </a>
                            <a v-if="order.client_email" :href="`mailto:${order.client_email}`"
                                class="flex items-center gap-2 text-sm text-on-surface-muted hover:text-primary">
                                <Mail class="w-3.5 h-3.5" /> {{ order.client_email }}
                            </a>
                            <div class="flex items-center gap-2 text-sm text-on-surface-muted">
                                <Truck class="w-3.5 h-3.5 text-primary shrink-0" />
                                {{ deliveryLabel(order.delivery_method) }}
                            </div>
                            <div v-if="order.delivery_address?.street || order.delivery_address?.city"
                                class="flex items-start gap-2 text-sm text-on-surface-muted">
                                <MapPin class="w-3.5 h-3.5 text-primary mt-0.5 shrink-0" />
                                <span>
                                    {{ [order.delivery_address?.city, order.delivery_address?.street, order.delivery_address?.apartment].filter(Boolean).join(', ') }}
                                </span>
                            </div>
                        </div>

                        <!-- Items -->
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-2">Состав заказа</p>
                            <div class="space-y-2">
                                <div v-for="item in order.items" :key="item.id"
                                    class="flex items-center justify-between gap-2 text-sm py-1.5 border-b border-outline last:border-0">
                                    <div class="flex items-start gap-2 min-w-0">
                                        <Package class="w-3.5 h-3.5 text-on-surface-muted mt-0.5 shrink-0" />
                                        <span class="text-on-surface truncate">{{ item.name }}</span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-on-surface-muted text-xs">{{ item.quantity }} шт. × </span>
                                        <span class="font-medium text-on-surface">{{ fmt(item.price) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comment -->
                    <div v-if="order.comment" class="mx-5 mb-4 p-3 rounded-lg bg-surface-muted/60 flex items-start gap-2">
                        <MessageSquare class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                        <p class="text-sm text-on-surface-muted italic">{{ order.comment }}</p>
                    </div>

                    <!-- Footer actions -->
                    <div class="border-t border-outline px-5 py-3 flex items-center gap-2 flex-wrap bg-surface-muted/30">
                        <template v-if="nextStatuses[getStatusVal(order)]">
                            <button
                                v-for="next in nextStatuses[getStatusVal(order)]"
                                :key="next.value"
                                @click="setStatus(order, next.value)"
                                :class="next.danger
                                    ? 'text-xs text-danger hover:underline px-2'
                                    : (next.value === 'completed' ? 'btn-primary !text-xs !py-1.5' : 'btn-secondary !text-xs !py-1.5')"
                            >
                                {{ next.label }}
                            </button>
                        </template>
                        <span v-else class="text-xs text-on-surface-muted">Статус финальный</span>
                    </div>
                </div>
            </div>

            <Pagination :links="orders.links" />
        </div>
    </CabinetLayout>
</template>
