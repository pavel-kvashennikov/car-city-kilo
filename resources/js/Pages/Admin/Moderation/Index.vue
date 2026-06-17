<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Badge from '@/Components/UI/Badge.vue'
import { router } from '@inertiajs/vue3'

defineProps({
    companies: { type: Object, default: () => ({ data: [] }) },
})

const columns = [
    { key: 'name', label: 'Название' },
    { key: 'inn', label: 'ИНН' },
    { key: 'owner', label: 'Владелец' },
    { key: 'created_at', label: 'Заявка' },
    { key: 'actions', label: '' },
]

const approve = (id) => router.post(`/admin/moderation/${id}/approve`)
const reject = (id) => router.post(`/admin/moderation/${id}/reject`)
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Модерация</h1>
        <DataTable :columns="columns" :rows="companies.data">
            <template #cell-owner="{ row }">{{ row.owner?.name || '—' }}</template>
            <template #cell-actions="{ row }">
                <div class="flex gap-2">
                    <button class="text-green-600 text-sm hover:underline" @click="approve(row.id)">Одобрить</button>
                    <button class="text-red-600 text-sm hover:underline" @click="reject(row.id)">Отклонить</button>
                </div>
            </template>
        </DataTable>
    </AdminLayout>
</template>
