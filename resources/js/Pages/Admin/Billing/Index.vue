<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    plans: { type: Array, default: () => [] },
    recentPayments: { type: Array, default: () => [] },
})

const planColumns = [
    { key: 'name', label: 'Тариф' },
    { key: 'price_monthly', label: 'Цена/мес' },
    { key: 'companies_count', label: 'Подписчики' },
    { key: 'actions', label: '' },
]

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Биллинг</h1>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Тарифные планы</h2>
                <Link href="/admin/billing/plans/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Добавить план
                </Link>
            </div>
            <DataTable :columns="planColumns" :rows="plans">
                <template #cell-price_monthly="{ value }">{{ formatPrice(value) }}</template>
                <template #cell-actions="{ row }">
                    <Link :href="`/admin/billing/plans/${row.id}/edit`" class="text-blue-600 text-sm hover:underline">Ред.</Link>
                </template>
            </DataTable>
        </div>
    </AdminLayout>
</template>
