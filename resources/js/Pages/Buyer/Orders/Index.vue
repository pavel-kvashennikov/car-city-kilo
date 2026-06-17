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

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Мои заказы</h1>
            <div v-if="orders.data.length === 0" class="text-center text-gray-500 py-12">
                У вас пока нет заказов
            </div>
            <div v-else class="space-y-4">
                <div v-for="order in orders.data" :key="order.id" class="bg-white rounded-xl shadow p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold">{{ order.order_number }}</span>
                        <Badge :variant="(statusMap[order.status] || statusMap.pending).variant">
                            {{ (statusMap[order.status] || statusMap.pending).label }}
                        </Badge>
                    </div>
                    <p class="text-sm text-gray-500">{{ order.created_at }}</p>
                    <p class="text-lg font-bold mt-2">{{ formatPrice(order.total_amount) }}</p>
                </div>
            </div>
            <div class="mt-6">
                <Pagination :links="orders.links" />
            </div>
        </div>
    </AppLayout>
</template>
