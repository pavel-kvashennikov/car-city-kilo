<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    companies: { type: Object, default: () => ({ data: [], links: [] }) },
})

const columns = [
    { key: 'name', label: 'Название' },
    { key: 'owner', label: 'Владелец' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Регистрация' },
    { key: 'actions', label: '' },
]

const statusVariant = (s) => {
    const map = { active: 'success', pending: 'warning', suspended: 'danger', rejected: 'danger' }
    return map[s] || 'default'
}
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Компании</h1>
        <DataTable :columns="columns" :rows="companies.data">
            <template #cell-owner="{ row }">{{ row.owner?.name || '—' }}</template>
            <template #cell-status="{ value }">
                <Badge :variant="statusVariant(value)">{{ value }}</Badge>
            </template>
            <template #cell-actions="{ row }">
                <Link :href="`/admin/companies/${row.id}`" class="text-blue-600 text-sm hover:underline">Управление</Link>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="companies.links" />
        </div>
    </AdminLayout>
</template>
