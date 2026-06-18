<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'

defineProps({
    plans: { type: Array, default: () => [] },
})

const columns = [
    { key: 'name', label: 'Тариф' },
    { key: 'price_monthly', label: 'Цена/мес' },
    { key: 'allowed_profiles', label: 'Профили' },
    { key: 'is_active', label: 'Статус' },
]

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Тарифные планы</h1>
        <DataTable :columns="columns" :rows="plans">
            <template #cell-price_monthly="{ value }">{{ formatPrice(value) }}</template>
            <template #cell-allowed_profiles="{ value }">
                {{ (value || []).join(', ') }}
            </template>
            <template #cell-is_active="{ value }">
                <Badge :variant="value ? 'success' : 'default'">{{ value ? 'Активен' : 'Неактивен' }}</Badge>
            </template>
        </DataTable>
    </AdminLayout>
</template>
