<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    vehicles: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'brand_name', label: 'Марка/Модель' },
    { key: 'year', label: 'Год' },
    { key: 'price', label: 'Цена' },
    { key: 'status', label: 'Статус' },
    { key: 'actions', label: '' },
]

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽'

const statusVariant = (status) => {
    const map = { active: 'success', draft: 'default', sold: 'info' }
    return map[status] || 'default'
}
</script>

<template>
    <CabinetLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Автомобили</h1>
            <Link href="/cabinet/dealer/vehicles/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Добавить авто
            </Link>
        </div>
        <DataTable :columns="columns" :rows="vehicles.data">
            <template #cell-brand_name="{ row }">
                {{ row.brand?.name }} {{ row.car_model?.name }}
            </template>
            <template #cell-price="{ value }">
                {{ formatPrice(value) }}
            </template>
            <template #cell-status="{ value }">
                <Badge :variant="statusVariant(value)">{{ value }}</Badge>
            </template>
            <template #cell-actions="{ row }">
                <Link :href="`/cabinet/dealer/vehicles/${row.id}/edit`" class="text-blue-600 text-sm hover:underline">
                    Редактировать
                </Link>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="vehicles.links" />
        </div>
    </CabinetLayout>
</template>
