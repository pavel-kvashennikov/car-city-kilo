<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'
import { ChevronLeft, Package, MapPin, MessageSquare, Calendar } from 'lucide-vue-next'

const props = defineProps({
    order: { type: Object, required: true },
})

const statusMap = {
    pending:    { label: 'Ожидает подтверждения', variant: 'warning' },
    processing: { label: 'В обработке',           variant: 'info' },
    completed:  { label: 'Выполнен',               variant: 'success' },
    cancelled:  { label: 'Отменён',                variant: 'danger' },
}

const deliveryMap = {
    pickup:   'Самовывоз',
    delivery: 'Доставка',
}

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v ?? 0) + ' ₽'
const fmtDate = (d) => d ? new Date(d).toLocaleString('ru-RU', {
    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
}) : '—'

const status = (s) => statusMap[s] ?? statusMap.pending
</script>

<template>
    <AppLayout>
        <div class="container-app max-w-3xl py-8 space-y-5">
            <!-- Breadcrumb -->
            <div>
                <Link href="/buyer/orders" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary">
                    <ChevronLeft class="w-3.5 h-3.5" /> Мои заказы
                </Link>
            </div>

            <!-- Header -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="page-title">Заказ {{ order.order_number }}</h1>
                    <p class="text-sm text-on-surface-muted flex items-center gap-1.5 mt-1">
                        <Calendar class="w-3.5 h-3.5" />
                        {{ fmtDate(order.created_at) }}
                    </p>
                </div>
                <Badge :variant="status(order.status?.value ?? order.status).variant">
                    {{ status(order.status?.value ?? order.status).label }}
                </Badge>
            </div>

            <!-- Items -->
            <div class="card">
                <div class="px-5 py-3 border-b border-outline flex items-center gap-2">
                    <Package class="w-4 h-4 text-primary" />
                    <span class="font-semibold text-sm">Состав заказа</span>
                    <span class="text-xs text-on-surface-muted ml-auto">{{ order.items?.length ?? 0 }} позиции</span>
                </div>
                <div class="divide-y divide-outline">
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="flex items-center justify-between px-5 py-3.5 gap-4"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-on-surface truncate">{{ item.name }}</p>
                            <p class="text-xs text-on-surface-muted mt-0.5">{{ fmt(item.price) }} × {{ item.quantity }}</p>
                        </div>
                        <p class="font-bold text-sm text-on-surface shrink-0">{{ fmt(item.total ?? item.price * item.quantity) }}</p>
                    </div>
                </div>
                <div class="px-5 py-3.5 border-t border-outline bg-surface-muted/40 flex justify-between items-center">
                    <span class="font-semibold text-sm text-on-surface-muted">Итого</span>
                    <span class="text-xl font-extrabold text-gradient">{{ fmt(order.total_amount) }}</span>
                </div>
            </div>

            <!-- Delivery -->
            <div class="card p-5 space-y-3">
                <h2 class="font-semibold text-sm flex items-center gap-2">
                    <MapPin class="w-4 h-4 text-primary" /> Доставка
                </h2>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-on-surface-muted uppercase tracking-wide font-semibold mb-0.5">Способ</p>
                        <p class="text-on-surface">{{ deliveryMap[order.delivery_method] ?? order.delivery_method ?? '—' }}</p>
                    </div>
                    <div v-if="order.delivery_address?.street || order.delivery_address?.city">
                        <p class="text-xs text-on-surface-muted uppercase tracking-wide font-semibold mb-0.5">Адрес</p>
                        <p class="text-on-surface">
                            {{ [order.delivery_address?.city, order.delivery_address?.street].filter(Boolean).join(', ') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-muted uppercase tracking-wide font-semibold mb-0.5">Получатель</p>
                        <p class="text-on-surface">{{ order.client_name || '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-muted uppercase tracking-wide font-semibold mb-0.5">Телефон</p>
                        <p class="text-on-surface">{{ order.client_phone || '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Comment -->
            <div v-if="order.comment" class="card p-5">
                <h2 class="font-semibold text-sm flex items-center gap-2 mb-2">
                    <MessageSquare class="w-4 h-4 text-primary" /> Комментарий
                </h2>
                <p class="text-sm text-on-surface leading-relaxed">{{ order.comment }}</p>
            </div>
        </div>
    </AppLayout>
</template>
