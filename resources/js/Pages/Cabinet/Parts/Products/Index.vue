<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { Plus, Upload } from 'lucide-vue-next';

defineProps({
    products: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'name', label: 'Название' },
    { key: 'article_number', label: 'Артикул' },
    { key: 'oem_number', label: 'OEM' },
    { key: 'price_retail', label: 'Цена (розн.)' },
    { key: 'stock_quantity', label: 'Остаток' },
    { key: 'actions', label: '' },
];

const fmt = (v) => v ? new Intl.NumberFormat('ru-RU').format(v) + ' ₽' : '—';
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Запчасти</h1>
                    <p class="page-subtitle">{{ products.total ?? products.data?.length ?? 0 }} товаров в каталоге</p>
                </div>
                <div class="flex gap-2">
                    <Link href="/cabinet/parts/products/import" class="btn-secondary !text-sm">
                        <Upload class="w-4 h-4" /> Импорт
                    </Link>
                    <Link href="/cabinet/parts/products/create" class="btn-primary !text-sm">
                        <Plus class="w-4 h-4" /> Добавить товар
                    </Link>
                </div>
            </div>

            <DataTable :columns="columns" :rows="products.data">
                <template #cell-name="{ row }">
                    <span class="font-semibold text-on-surface text-xs">{{ row.name }}</span>
                </template>
                <template #cell-article_number="{ value }">
                    <span class="font-mono text-xs text-on-surface-muted">{{ value || '—' }}</span>
                </template>
                <template #cell-oem_number="{ value }">
                    <span class="font-mono text-xs text-on-surface-muted">{{ value || '—' }}</span>
                </template>
                <template #cell-price_retail="{ value }">
                    <span class="font-semibold">{{ fmt(value) }}</span>
                </template>
                <template #cell-stock_quantity="{ value }">
                    <Badge :variant="value > 0 ? 'success' : 'warning'">{{ value }}</Badge>
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex gap-3">
                        <Link :href="`/cabinet/parts/products/${row.id}/edit`" class="text-xs font-semibold text-primary hover:underline">
                            Изменить
                        </Link>
                        <Link :href="`/catalog/parts/${row.slug}`" target="_blank" class="text-xs text-on-surface-muted hover:text-primary">↗</Link>
                    </div>
                </template>
            </DataTable>

            <div v-if="products.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="page in products.links"
                    :key="page.label"
                    :href="page.url || '#'"
                    v-html="page.label"
                    class="min-w-[36px] h-9 flex items-center justify-center px-3 rounded-lg text-sm border transition-colors"
                    :class="page.active
                        ? 'border-primary bg-primary-light text-primary font-semibold'
                        : page.url
                            ? 'border-outline text-on-surface-muted hover:border-primary hover:text-primary'
                            : 'border-transparent text-outline cursor-not-allowed'"
                />
            </div>
        </div>
    </CabinetLayout>
</template>
