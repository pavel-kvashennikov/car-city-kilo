<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

defineProps({
    vehicles: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'photo', label: '' },
    { key: 'brand_name', label: 'Марка / Модель' },
    { key: 'year', label: 'Год' },
    { key: 'price', label: 'Цена' },
    { key: 'status', label: 'Статус' },
    { key: 'actions', label: '' },
];

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽';
const statusVariant = (s) => ({ active: 'success', draft: 'default', sold: 'info' }[s] || 'default');
const statusLabel = (s) => ({ active: 'Активен', draft: 'Черновик', sold: 'Продан', inactive: 'Неактивен' }[s] || s);
const photoSrc = (v) => {
    const path = v.photos?.[0]?.path;
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Автомобили</h1>
                    <p class="page-subtitle">{{ vehicles.total ?? vehicles.data?.length ?? 0 }} объявлений</p>
                </div>
                <Link href="/cabinet/dealer/vehicles/create" class="btn-primary !text-sm">
                    <Plus class="w-4 h-4" /> Добавить авто
                </Link>
            </div>

            <DataTable :columns="columns" :rows="vehicles.data">
                <template #cell-photo="{ row }">
                    <div class="w-12 h-10 rounded-lg overflow-hidden bg-surface-muted shrink-0">
                        <img v-if="photoSrc(row)" :src="photoSrc(row)" class="w-full h-full object-cover" />
                    </div>
                </template>
                <template #cell-brand_name="{ row }">
                    <span class="font-semibold text-on-surface">{{ row.brand?.name }} {{ row.car_model?.name }}</span>
                </template>
                <template #cell-price="{ value }">
                    <span class="font-semibold">{{ fmt(value) }}</span>
                </template>
                <template #cell-status="{ value }">
                    <Badge :variant="statusVariant(value)">{{ statusLabel(value) }}</Badge>
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center gap-3">
                        <Link :href="`/cabinet/dealer/vehicles/${row.id}/edit`" class="text-xs font-semibold text-primary hover:underline">
                            Изменить
                        </Link>
                        <Link :href="`/catalog/cars/${row.slug}`" target="_blank" class="text-xs text-on-surface-muted hover:text-primary">
                            ↗
                        </Link>
                    </div>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div v-if="vehicles.last_page > 1" class="flex items-center justify-center gap-1">
                <Link
                    v-for="page in vehicles.links"
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
