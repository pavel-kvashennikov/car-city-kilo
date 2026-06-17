<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'

defineProps({
    staff: { type: Array, default: () => [] },
})

const columns = [
    { key: 'name', label: 'Имя' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Роль' },
]
</script>

<template>
    <CabinetLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Сотрудники</h1>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Добавить
            </button>
        </div>
        <DataTable :columns="columns" :rows="staff">
            <template #cell-role="{ value }">
                <Badge>{{ value }}</Badge>
            </template>
        </DataTable>
    </CabinetLayout>
</template>
