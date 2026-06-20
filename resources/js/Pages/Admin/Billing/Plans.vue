<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';

defineProps({
    plans: { type: Array, default: () => [] },
});

const columns = [
    { key: 'name', label: 'Тариф' },
    { key: 'price_monthly', label: 'Цена/мес' },
    { key: 'allowed_profiles', label: 'Профили' },
    { key: 'is_active', label: 'Статус' },
];

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽';
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Тарифные планы</h1>
                <p class="page-subtitle">Доступные подписки для резидентов</p>
            </div>

            <DataTable :columns="columns" :rows="plans">
                <template #cell-name="{ row }">
                    <span class="font-semibold text-on-surface text-sm">{{ row.name }}</span>
                </template>
                <template #cell-price_monthly="{ value }">
                    <span class="font-semibold">{{ fmt(value) }}</span>
                </template>
                <template #cell-allowed_profiles="{ value }">
                    <div class="flex flex-wrap gap-1">
                        <Badge v-for="p in (value || [])" :key="p" variant="info">{{ p }}</Badge>
                    </div>
                </template>
                <template #cell-is_active="{ value }">
                    <Badge :variant="value ? 'success' : 'default'">{{ value ? 'Активен' : 'Скрыт' }}</Badge>
                </template>
            </DataTable>
        </div>
    </AdminLayout>
</template>
