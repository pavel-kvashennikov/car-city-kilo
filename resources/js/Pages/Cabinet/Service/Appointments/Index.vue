<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import { router } from '@inertiajs/vue3'

defineProps({
    appointments: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'contact_name', label: 'Клиент' },
    { key: 'contact_phone', label: 'Телефон' },
    { key: 'date', label: 'Дата' },
    { key: 'time', label: 'Время' },
    { key: 'status', label: 'Статус' },
    { key: 'actions', label: '' },
]

const confirm = (id) => {
    router.patch(`/cabinet/service/appointments/${id}`, { status: 'confirmed' })
}

const statusVariant = (s) => {
    const map = { pending: 'warning', confirmed: 'success', completed: 'info', cancelled: 'danger' }
    return map[s] || 'default'
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Записи</h1>
        <DataTable :columns="columns" :rows="appointments.data">
            <template #cell-date="{ row }">{{ row.time_slot?.date }}</template>
            <template #cell-time="{ row }">{{ row.time_slot?.start_time }} — {{ row.time_slot?.end_time }}</template>
            <template #cell-status="{ value }">
                <Badge :variant="statusVariant(value)">{{ value }}</Badge>
            </template>
            <template #cell-actions="{ row }">
                <button
                    v-if="row.status === 'pending'"
                    class="text-green-600 text-sm hover:underline"
                    @click="confirm(row.id)"
                >
                    Подтвердить
                </button>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="appointments.links" />
        </div>
    </CabinetLayout>
</template>
