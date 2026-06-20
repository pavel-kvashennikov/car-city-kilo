<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    companies: { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const columns = [
    { key: 'name', label: 'Название' },
    { key: 'owner', label: 'Владелец' },
    { key: 'status', label: 'Статус' },
    { key: 'created_at', label: 'Регистрация' },
    { key: 'actions', label: '' },
];

const statusVariant = (s) => ({ active: 'success', pending: 'warning', suspended: 'danger', rejected: 'danger' }[s] || 'default');
const statusLabel = (s) => ({ active: 'Активна', pending: 'На проверке', suspended: 'Заблокирована', rejected: 'Отклонена' }[s] || s);

const applyFilters = () => {
    router.get('/admin/companies', { search: search.value || undefined, status: status.value || undefined }, { preserveState: true, replace: true });
};

const approve = (id) => router.put(`/admin/companies/${id}/approve`, {}, { preserveScroll: true });
const suspend = (id) => {
    if (confirm('Заблокировать компанию?')) router.put(`/admin/companies/${id}/suspend`, {}, { preserveScroll: true });
};
const reject = (id) => {
    const reason = prompt('Причина отклонения:');
    if (reason) router.put(`/admin/companies/${id}/reject`, { reason }, { preserveScroll: true });
};

const statusOptions = [
    { v: '', l: 'Все статусы' },
    { v: 'pending', l: 'На проверке' },
    { v: 'active', l: 'Активные' },
    { v: 'suspended', l: 'Заблокированные' },
    { v: 'rejected', l: 'Отклонённые' },
];
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="page-title">Компании</h1>
                    <p class="page-subtitle">{{ companies.total ?? companies.data.length }} зарегистрировано</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="search-pill">
                        <Search class="w-3.5 h-3.5 text-secondary shrink-0" />
                        <input v-model="search" type="text" placeholder="Поиск..." class="bg-transparent border-none outline-none text-sm w-32" @keyup.enter="applyFilters" />
                    </div>
                    <select v-model="status" class="input-field !w-auto !py-2 text-sm" @change="applyFilters">
                        <option v-for="o in statusOptions" :key="o.v" :value="o.v">{{ o.l }}</option>
                    </select>
                </div>
            </div>

            <DataTable :columns="columns" :rows="companies.data">
                <template #cell-name="{ row }">
                    <span class="font-semibold text-on-surface text-sm">{{ row.name }}</span>
                </template>
                <template #cell-owner="{ row }">
                    <span class="text-on-surface-muted">{{ row.owner?.name || '—' }}</span>
                </template>
                <template #cell-status="{ value }">
                    <Badge :variant="statusVariant(value)">{{ statusLabel(value) }}</Badge>
                </template>
                <template #cell-created_at="{ value }">
                    <span class="text-on-surface-muted text-xs">{{ value ? new Date(value).toLocaleDateString('ru-RU') : '—' }}</span>
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center gap-3 justify-end">
                        <button v-if="row.status === 'pending'" class="text-xs font-semibold text-success hover:underline" @click="approve(row.id)">Одобрить</button>
                        <button v-if="row.status === 'pending'" class="text-xs text-danger hover:underline" @click="reject(row.id)">Отклонить</button>
                        <button v-if="row.status === 'active'" class="text-xs text-danger hover:underline" @click="suspend(row.id)">Заблокировать</button>
                        <button v-if="row.status === 'suspended'" class="text-xs font-semibold text-success hover:underline" @click="approve(row.id)">Разблокировать</button>
                    </div>
                </template>
            </DataTable>
            <Pagination :links="companies.links" />
        </div>
    </AdminLayout>
</template>
