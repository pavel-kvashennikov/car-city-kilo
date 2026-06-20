<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    orders: { type: Object, default: () => ({ data: [], links: [] }) },
})

const statusMap = {
    pending: { label: 'Ожидает', variant: 'warning' },
    processing: { label: 'В обработке', variant: 'info' },
    completed: { label: 'Выполнен', variant: 'success' },
    cancelled: { label: 'Отменён', variant: 'danger' },
}

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽'
</script>

<template>
    <AppLayout>
        <div class="container-app max-w-4xl py-8">
            <h1 class="page-title mb-6">Мои заказы</h1>
            <div v-if="orders.data.length === 0" class="card p-12 text-center text-on-surface-muted">
                У вас пока нет заказов
            </div>
            <div v-else class="space-y-3">
                <div v-for="order in orders.data" :key="order.id" class="card p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono font-semibold text-on-surface">{{ order.order_number }}</span>
                        <Badge :variant="(statusMap[order.status] || statusMap.pending).variant">
                            {{ (statusMap[order.status] || statusMap.pending).label }}
                        </Badge>
                    </div>
                    <p class="text-sm text-on-surface-muted">{{ order.created_at }}</p>
                    <p class="text-lg font-extrabold text-gradient mt-2">{{ formatPrice(order.total_amount) }}</p>
                </div>
            </div>
            <div class="mt-6">
                <Pagination :links="orders.links" />
            </div>
        </div>
    </AppLayout>
</template>
