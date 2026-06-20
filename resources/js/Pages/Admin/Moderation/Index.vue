<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { CheckCircle2, ShieldCheck } from 'lucide-vue-next';

defineProps({
    pendingVehicles: { type: Object, default: () => ({ data: [], links: [] }) },
});

const columns = [
    { key: 'vehicle', label: 'Автомобиль' },
    { key: 'company', label: 'Компания' },
    { key: 'price', label: 'Цена' },
    { key: 'created_at', label: 'Заявка' },
    { key: 'actions', label: '' },
];

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽';
const approve = (id) => router.put(`/admin/moderation/vehicles/${id}/approve`, {}, { preserveScroll: true });
const reject = (id) => {
    if (confirm('Отклонить объявление?')) router.put(`/admin/moderation/vehicles/${id}/reject`, {}, { preserveScroll: true });
};
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Модерация</h1>
                <p class="page-subtitle">Объявления, ожидающие публикации</p>
            </div>

            <div v-if="!pendingVehicles.data.length" class="card p-12 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-xl bg-success-bg flex items-center justify-center mb-3">
                    <ShieldCheck class="w-6 h-6 text-success" />
                </div>
                <p class="font-semibold text-on-surface">Очередь пуста</p>
                <p class="text-sm text-on-surface-muted mt-1">Все объявления проверены</p>
            </div>

            <template v-else>
                <DataTable :columns="columns" :rows="pendingVehicles.data">
                    <template #cell-vehicle="{ row }">
                        <span class="font-semibold text-on-surface text-sm">{{ row.brand?.name }} {{ row.car_model?.name }} {{ row.year }}</span>
                    </template>
                    <template #cell-company="{ row }">
                        <span class="text-on-surface-muted">{{ row.dealer_profile?.company?.name ?? '—' }}</span>
                    </template>
                    <template #cell-price="{ value }">
                        <span class="font-semibold">{{ fmt(value) }}</span>
                    </template>
                    <template #cell-created_at="{ value }">
                        <span class="text-on-surface-muted text-xs">{{ value ? new Date(value).toLocaleDateString('ru-RU') : '—' }}</span>
                    </template>
                    <template #cell-actions="{ row }">
                        <div class="flex items-center gap-3 justify-end">
                            <button class="inline-flex items-center gap-1 text-xs font-semibold text-success hover:underline" @click="approve(row.id)">
                                <CheckCircle2 class="w-3.5 h-3.5" /> Опубликовать
                            </button>
                            <button class="text-xs text-danger hover:underline" @click="reject(row.id)">Отклонить</button>
                        </div>
                    </template>
                </DataTable>
                <Pagination :links="pendingVehicles.links" />
            </template>
        </div>
    </AdminLayout>
</template>
