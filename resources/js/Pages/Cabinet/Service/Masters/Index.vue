<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

defineProps({
    masters: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'name', label: 'Имя' },
    { key: 'specialization', label: 'Специализация' },
    { key: 'is_active', label: 'Статус' },
    { key: 'actions', label: '' },
];
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Мастера</h1>
                    <p class="page-subtitle">Сотрудники сервиса для записи клиентов</p>
                </div>
                <Link href="/cabinet/service/masters/create" class="btn-primary !text-sm">
                    <Plus class="w-4 h-4" /> Добавить мастера
                </Link>
            </div>

            <DataTable :columns="columns" :rows="masters.data ?? masters">
                <template #cell-name="{ row }">
                    <span class="font-semibold text-on-surface text-sm">{{ row.name }}</span>
                </template>
                <template #cell-specialization="{ value }">
                    <span class="text-on-surface-muted">{{ value || '—' }}</span>
                </template>
                <template #cell-is_active="{ value }">
                    <Badge :variant="value ? 'success' : 'default'">{{ value ? 'Активен' : 'Неактивен' }}</Badge>
                </template>
                <template #cell-actions="{ row }">
                    <Link :href="`/cabinet/service/masters/${row.id}/edit`" class="text-xs font-semibold text-primary hover:underline">Изменить</Link>
                </template>
            </DataTable>
        </div>
    </CabinetLayout>
</template>
