<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

defineProps({
    staff: { type: Array, default: () => [] },
});

const columns = [
    { key: 'name', label: 'Имя' },
    { key: 'email', label: 'Email' },
    { key: 'position', label: 'Должность' },
    { key: 'actions', label: '' },
];
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Сотрудники</h1>
                    <p class="page-subtitle">Доступ сотрудников к управлению компанией</p>
                </div>
                <Link href="/cabinet/staff/create" class="btn-primary !text-sm">
                    <Plus class="w-4 h-4" /> Добавить
                </Link>
            </div>

            <DataTable :columns="columns" :rows="staff">
                <template #cell-name="{ row }">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-full bg-primary-light text-primary font-bold text-xs flex items-center justify-center shrink-0">
                            {{ row.name?.charAt(0) }}
                        </span>
                        <span class="font-semibold text-on-surface text-sm">{{ row.name }}</span>
                    </div>
                </template>
                <template #cell-email="{ value }">
                    <span class="text-on-surface-muted">{{ value }}</span>
                </template>
                <template #cell-position="{ row }">
                    <Badge>{{ row.pivot?.position ?? '—' }}</Badge>
                </template>
                <template #cell-actions="{ row }">
                    <Link :href="`/cabinet/staff/${row.id}/edit`" class="text-xs font-semibold text-primary hover:underline">Изменить</Link>
                </template>
            </DataTable>
        </div>
    </CabinetLayout>
</template>
