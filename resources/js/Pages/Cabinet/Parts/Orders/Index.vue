<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import Pagination from '@/Components/UI/Pagination.vue'

defineProps({
    orders: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'order_number', label: '№ заказа' },
    { key: 'contact_name', label: 'Клиент' },
    { key: 'total_amount', label: 'Сумма' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Дата' },
]

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Заказы</h1>
        <DataTable :columns="columns" :rows="orders.data">
            <template #cell-total_amount="{ value }">
                {{ formatPrice(value) }}
            </template>
            <template #cell-status="{ value }">
                <Badge :variant="value === 'completed' ? 'success' : value === 'pending' ? 'warning' : 'info'">
                    {{ value }}
                </Badge>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="orders.links" />
        </div>
    </CabinetLayout>
</template>
