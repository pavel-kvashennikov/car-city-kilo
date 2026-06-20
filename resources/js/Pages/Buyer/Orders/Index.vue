<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'
import { Package, ChevronRight, ShoppingBag } from 'lucide-vue-next'

defineProps({
    orders: { type: Object, default: () => ({ data: [], links: [] }) },
})

const statusMap = {
    pending:    { label: 'Ожидает',     variant: 'warning' },
    processing: { label: 'В обработке', variant: 'info' },
    completed:  { label: 'Выполнен',    variant: 'success' },
    cancelled:  { label: 'Отменён',     variant: 'danger' },
}

const getStatus = (s) => statusMap[s?.value ?? s] ?? statusMap.pending
const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v ?? 0) + ' ₽'
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric' }) : '—'
</script>

<template>
    <AppLayout>
        <div class="container-app max-w-3xl py-8">
            <h1 class="page-title mb-6">Мои заказы</h1>

            <div v-if="!orders.data?.length" class="card p-16 text-center">
                <ShoppingBag class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">У вас пока нет заказов</p>
                <Link href="/catalog/parts" class="btn-primary mt-4 inline-flex">
                    Перейти в каталог
                </Link>
            </div>

            <div v-else class="space-y-3">
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="`/buyer/orders/${order.id}`"
                    class="card card-hover p-5 block"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <Package class="w-3.5 h-3.5 text-primary shrink-0" />
                                <span class="font-mono font-bold text-on-surface text-sm">{{ order.order_number }}</span>
                                <Badge :variant="getStatus(order.status).variant" class="ml-1">
                                    {{ getStatus(order.status).label }}
                                </Badge>
                            </div>
                            <p class="text-xs text-on-surface-muted">{{ fmtDate(order.created_at) }}</p>
                            <p v-if="order.items?.length" class="text-xs text-on-surface-muted mt-1">
                                {{ order.items.length }} {{ order.items.length === 1 ? 'позиция' : 'позиции' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-lg font-extrabold text-gradient">{{ fmt(order.total_amount) }}</span>
                            <ChevronRight class="w-4 h-4 text-on-surface-muted" />
                        </div>
                    </div>
                </Link>
            </div>

            <div class="mt-6">
                <Pagination :links="orders.links" />
            </div>
        </div>
    </AppLayout>
</template>
