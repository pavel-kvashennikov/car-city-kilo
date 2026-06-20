<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';

defineProps({
    plans: { type: Array, default: () => [] },
    recentPayments: { type: Array, default: () => [] },
});

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽';

const planColumns = [
    { key: 'name', label: 'Тариф' },
    { key: 'price_monthly', label: 'Цена/мес' },
    { key: 'companies_count', label: 'Подписчики' },
    { key: 'is_active', label: 'Статус' },
];

const paymentColumns = [
    { key: 'company', label: 'Компания' },
    { key: 'amount', label: 'Сумма' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Дата' },
];

const payStatus = (s) => ({ paid: 'success', pending: 'warning', failed: 'danger' }[s] || 'default');
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="page-title">Биллинг</h1>
                <p class="page-subtitle">Тарифы и платежи платформы</p>
            </div>

            <div>
                <h2 class="font-bold text-sm text-on-surface mb-3">Тарифные планы</h2>
                <DataTable :columns="planColumns" :rows="plans">
                    <template #cell-name="{ row }">
                        <span class="font-semibold text-on-surface text-sm">{{ row.name }}</span>
                    </template>
                    <template #cell-price_monthly="{ value }">
                        <span class="font-semibold">{{ fmt(value) }}</span>
                    </template>
                    <template #cell-companies_count="{ value }">
                        <Badge variant="info">{{ value ?? 0 }}</Badge>
                    </template>
                    <template #cell-is_active="{ value }">
                        <Badge :variant="value ? 'success' : 'default'">{{ value ? 'Активен' : 'Скрыт' }}</Badge>
                    </template>
                </DataTable>
            </div>

            <div v-if="recentPayments.length">
                <h2 class="font-bold text-sm text-on-surface mb-3">Последние платежи</h2>
                <DataTable :columns="paymentColumns" :rows="recentPayments">
                    <template #cell-company="{ row }">
                        <span class="font-semibold text-on-surface text-sm">{{ row.company?.name ?? '—' }}</span>
                    </template>
                    <template #cell-amount="{ value }">
                        <span class="font-semibold">{{ fmt(value) }}</span>
                    </template>
                    <template #cell-status="{ value }">
                        <Badge :variant="payStatus(value)">{{ value }}</Badge>
                    </template>
                    <template #cell-created_at="{ value }">
                        <span class="text-on-surface-muted text-xs">{{ value ? new Date(value).toLocaleDateString('ru-RU') : '—' }}</span>
                    </template>
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>
