<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    leads: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'client_name', label: 'Имя' },
    { key: 'client_phone', label: 'Телефон' },
    { key: 'lead_type', label: 'Тип' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Дата' },
    { key: 'actions', label: '' },
];

const typeLabel = (t) => ({ test_drive: 'Тест-драйв', credit: 'Кредит', trade_in: 'Trade-in', callback: 'Звонок' }[t] || t);
const typeVariant = (t) => ({ test_drive: 'info', credit: 'warning', trade_in: 'default' }[t] || 'default');
const statusLabel = (s) => ({ new: 'Новый', in_progress: 'В работе', done: 'Обработан', rejected: 'Отклонён' }[s] || s);
const statusVariant = (s) => ({ new: 'warning', in_progress: 'info', done: 'success', rejected: 'danger' }[s] || 'default');

const markDone = (id) => router.put(`/cabinet/dealer/leads/${id}`, { status: 'done' });
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Лиды</h1>
                <p class="page-subtitle">Заявки от покупателей: тест-драйв, кредит, trade-in</p>
            </div>

            <DataTable :columns="columns" :rows="leads.data">
                <template #cell-client_name="{ row }">
                    <span class="font-semibold text-on-surface text-sm">{{ row.client_name ?? row.name }}</span>
                </template>
                <template #cell-client_phone="{ row }">
                    <a :href="`tel:${row.client_phone ?? row.phone}`" class="text-primary">{{ row.client_phone ?? row.phone }}</a>
                </template>
                <template #cell-lead_type="{ row }">
                    <Badge :variant="typeVariant(row.lead_type ?? row.type)">{{ typeLabel(row.lead_type ?? row.type) }}</Badge>
                </template>
                <template #cell-status="{ value }">
                    <Badge :variant="statusVariant(value)">{{ statusLabel(value) }}</Badge>
                </template>
                <template #cell-created_at="{ value }">
                    <span class="text-on-surface-muted text-xs">{{ value ? new Date(value).toLocaleDateString('ru-RU') : '—' }}</span>
                </template>
                <template #cell-actions="{ row }">
                    <button v-if="row.status === 'new'" class="text-xs font-semibold text-success hover:underline" @click="markDone(row.id)">
                        Обработан
                    </button>
                </template>
            </DataTable>
            <Pagination :links="leads.links" />
        </div>
    </CabinetLayout>
</template>
