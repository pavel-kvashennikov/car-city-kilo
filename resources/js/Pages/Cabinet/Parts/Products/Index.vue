<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    products: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'name', label: 'Название' },
    { key: 'article_number', label: 'Артикул' },
    { key: 'oem_number', label: 'OEM' },
    { key: 'price_retail', label: 'Цена' },
    { key: 'stock_quantity', label: 'Остаток' },
    { key: 'actions', label: '' },
]

const formatPrice = (v) => v ? new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽' : '—'
</script>

<template>
    <CabinetLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Товары</h1>
            <div class="flex gap-2">
                <Link href="/cabinet/parts/products/import" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                    Импорт
                </Link>
                <Link href="/cabinet/parts/products/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Добавить товар
                </Link>
            </div>
        </div>
        <DataTable :columns="columns" :rows="products.data">
            <template #cell-price_retail="{ value }">
                {{ formatPrice(value) }}
            </template>
            <template #cell-stock_quantity="{ value }">
                <Badge :variant="value > 0 ? 'success' : 'danger'">{{ value }}</Badge>
            </template>
            <template #cell-actions="{ row }">
                <Link :href="`/cabinet/parts/products/${row.id}/edit`" class="text-blue-600 text-sm hover:underline">
                    Ред.
                </Link>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="products.links" />
        </div>
    </CabinetLayout>
</template>
