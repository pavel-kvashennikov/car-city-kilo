<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/UI/Pagination.vue';

defineProps({
    orders: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'order_number', label: '№ заказа' },
    { key: 'contact_name', label: 'Клиент' },
    { key: 'total_amount', label: 'Сумма' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Дата' },
];

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽';
const statusLabel = (s) => ({ pending: 'Ожидает', processing: 'В обработке', completed: 'Выполнен', cancelled: 'Отменён' }[s] || s);
const statusVariant = (s) => ({ completed: 'success', pending: 'warning', processing: 'info', cancelled: 'danger' }[s] || 'default');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Заказы</h1>
                <p class="page-subtitle">Заказы запчастей от покупателей</p>
            </div>

            <DataTable :columns="columns" :rows="orders.data">
                <template #cell-order_number="{ value }">
                    <span class="font-mono font-semibold text-on-surface text-xs">{{ value }}</span>
                </template>
                <template #cell-total_amount="{ value }">
                    <span class="font-semibold">{{ fmt(value) }}</span>
                </template>
                <template #cell-status="{ value }">
                    <Badge :variant="statusVariant(value)">{{ statusLabel(value) }}</Badge>
                </template>
                <template #cell-created_at="{ value }">
                    <span class="text-on-surface-muted text-xs">{{ value ? new Date(value).toLocaleDateString('ru-RU') : '—' }}</span>
                </template>
            </DataTable>
            <Pagination :links="orders.links" />
        </div>
    </CabinetLayout>
</template>
