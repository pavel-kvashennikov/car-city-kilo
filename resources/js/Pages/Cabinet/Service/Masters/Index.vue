<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    masters: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'name', label: 'Имя' },
    { key: 'specialization', label: 'Специализация' },
    { key: 'is_active', label: 'Статус' },
    { key: 'actions', label: '' },
]
</script>

<template>
    <CabinetLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Мастера</h1>
            <Link href="/cabinet/service/masters/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Добавить мастера
            </Link>
        </div>
        <DataTable :columns="columns" :rows="masters.data ?? masters">
            <template #cell-is_active="{ value }">
                <Badge :variant="value ? 'success' : 'default'">{{ value ? 'Активен' : 'Неактивен' }}</Badge>
            </template>
            <template #cell-actions="{ row }">
                <Link :href="`/cabinet/service/masters/${row.id}/edit`" class="text-blue-600 text-sm hover:underline">Ред.</Link>
            </template>
        </DataTable>
    </CabinetLayout>
</template>
