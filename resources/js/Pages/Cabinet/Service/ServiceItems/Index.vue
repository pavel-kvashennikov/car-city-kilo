<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

defineProps({
    items: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'name', label: 'Услуга' },
    { key: 'price_fixed', label: 'Цена' },
    { key: 'duration_minutes', label: 'Длительность' },
    { key: 'actions', label: '' },
];

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽';
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Услуги</h1>
                    <p class="page-subtitle">Прайс-лист сервиса</p>
                </div>
                <Link href="/cabinet/service/items/create" class="btn-primary !text-sm">
                    <Plus class="w-4 h-4" /> Добавить услугу
                </Link>
            </div>

            <DataTable :columns="columns" :rows="items.data ?? items">
                <template #cell-name="{ row }">
                    <div>
                        <span class="font-semibold text-on-surface text-sm">{{ row.name }}</span>
                        <p v-if="row.description" class="text-xs text-on-surface-muted line-clamp-1 mt-0.5">{{ row.description }}</p>
                    </div>
                </template>
                <template #cell-price_fixed="{ row }">
                    <span class="font-semibold">
                        <template v-if="row.price_fixed">{{ fmt(row.price_fixed) }}</template>
                        <template v-else-if="row.price_from">от {{ fmt(row.price_from) }}</template>
                        <template v-else>—</template>
                    </span>
                </template>
                <template #cell-duration_minutes="{ value }">{{ value }} мин</template>
                <template #cell-actions="{ row }">
                    <Link :href="`/cabinet/service/items/${row.id}/edit`" class="text-xs font-semibold text-primary hover:underline">Изменить</Link>
                </template>
            </DataTable>
        </div>
    </CabinetLayout>
</template>
