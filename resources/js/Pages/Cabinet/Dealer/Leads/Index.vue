<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import Pagination from '@/Components/UI/Pagination.vue'

defineProps({
    leads: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'name', label: 'Имя' },
    { key: 'phone', label: 'Телефон' },
    { key: 'type', label: 'Тип' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Дата' },
]
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Лиды</h1>
        <DataTable :columns="columns" :rows="leads.data">
            <template #cell-type="{ value }">
                <Badge :variant="value === 'test_drive' ? 'info' : 'default'">{{ value }}</Badge>
            </template>
            <template #cell-status="{ value }">
                <Badge :variant="value === 'new' ? 'warning' : 'success'">{{ value }}</Badge>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="leads.links" />
        </div>
    </CabinetLayout>
</template>
