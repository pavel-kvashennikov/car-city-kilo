<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    appointments: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'contact_name', label: 'Клиент' },
    { key: 'contact_phone', label: 'Телефон' },
    { key: 'date', label: 'Дата' },
    { key: 'time', label: 'Время' },
    { key: 'status', label: 'Статус' },
    { key: 'actions', label: '' },
];

const confirm_ = (id) => router.patch(`/cabinet/service/appointments/${id}`, { status: 'confirmed' });
const cancel_ = (id) => router.patch(`/cabinet/service/appointments/${id}`, { status: 'cancelled' });

const statusVariant = (s) => ({ pending: 'warning', confirmed: 'success', completed: 'info', cancelled: 'danger' }[s] || 'default');
const statusLabel = (s) => ({ pending: 'Ожидает', confirmed: 'Подтверждена', completed: 'Завершена', cancelled: 'Отменена' }[s] || s);
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
const fmtTime = (t) => t ? String(t).slice(0, 5) : '';
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Записи на сервис</h1>
                <p class="page-subtitle">Управление заявками клиентов</p>
            </div>

            <DataTable :columns="columns" :rows="appointments.data">
                <template #cell-contact_name="{ value }">
                    <span class="font-semibold text-on-surface text-sm">{{ value }}</span>
                </template>
                <template #cell-contact_phone="{ value }">
                    <a :href="`tel:${value}`" class="text-primary">{{ value }}</a>
                </template>
                <template #cell-date="{ row }">{{ fmtDate(row.time_slot?.date) }}</template>
                <template #cell-time="{ row }">{{ fmtTime(row.time_slot?.start_time) }} — {{ fmtTime(row.time_slot?.end_time) }}</template>
                <template #cell-status="{ value }">
                    <Badge :variant="statusVariant(value)">{{ statusLabel(value) }}</Badge>
                </template>
                <template #cell-actions="{ row }">
                    <div v-if="row.status === 'pending'" class="flex items-center gap-3">
                        <button class="text-xs font-semibold text-success hover:underline" @click="confirm_(row.id)">Подтвердить</button>
                        <button class="text-xs text-danger hover:underline" @click="cancel_(row.id)">Отклонить</button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="appointments.links" />
        </div>
    </CabinetLayout>
</template>
