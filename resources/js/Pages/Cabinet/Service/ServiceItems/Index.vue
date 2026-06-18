<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    items: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'name', label: 'Услуга' },
    { key: 'price_fixed', label: 'Цена' },
    { key: 'duration_minutes', label: 'Длительность' },
    { key: 'actions', label: '' },
]

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽'
</script>

<template>
    <CabinetLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Услуги</h1>
            <Link href="/cabinet/service/items/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Добавить услугу
            </Link>
        </div>
        <DataTable :columns="columns" :rows="items.data ?? items">
            <template #cell-price_fixed="{ value }">{{ value ? formatPrice(value) : '—' }}</template>
            <template #cell-duration_minutes="{ value }">{{ value }} мин</template>
            <template #cell-actions="{ row }">
                <Link :href="`/cabinet/service/items/${row.id}/edit`" class="text-blue-600 text-sm hover:underline">Ред.</Link>
            </template>
        </DataTable>
    </CabinetLayout>
</template>
