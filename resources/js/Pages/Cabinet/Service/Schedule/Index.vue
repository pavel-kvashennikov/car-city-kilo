<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { useForm } from '@inertiajs/vue3';
import { CalendarPlus } from 'lucide-vue-next';

const props = defineProps({
    slots: { type: Object, default: () => ({ data: [], links: [] }) },
    masters: { type: Array, default: () => [] },
});

const form = useForm({
    date_from: '',
    date_to: '',
    start_time: '09:00',
    end_time: '18:00',
    slot_duration: 60,
    master_id: '',
});

const columns = [
    { key: 'date', label: 'Дата' },
    { key: 'time', label: 'Время' },
    { key: 'master', label: 'Мастер' },
    { key: 'status', label: 'Статус' },
];

const statusVariant = (s) => ({ available: 'success', booked: 'warning', blocked: 'danger' }[s] ?? 'default');
const statusLabel = (s) => ({ available: 'Свободен', booked: 'Занят', blocked: 'Заблокирован' }[s] ?? s);
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
const fmtTime = (t) => t ? String(t).slice(0, 5) : '';

const generate = () => form.post('/cabinet/service/schedule/slots');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Расписание</h1>
                <p class="page-subtitle">Сгенерируйте слоты для онлайн-записи</p>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="generate">
                <h2 class="font-bold text-sm text-on-surface flex items-center gap-2">
                    <CalendarPlus class="w-4 h-4 text-primary" /> Генерация слотов
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <Input v-model="form.date_from" label="Дата с" type="date" required />
                    <Input v-model="form.date_to" label="Дата по" type="date" required />
                    <Input v-model="form.start_time" label="Начало" type="time" />
                    <Input v-model="form.end_time" label="Конец" type="time" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.slot_duration" label="Длительность слота, мин" type="number" />
                    <Select v-model="form.master_id" label="Мастер" placeholder="Любой" :options="masters" option-value="id" option-label="name" />
                </div>
                <div class="pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Создать слоты</Button>
                </div>
            </form>

            <div class="card overflow-hidden">
                <DataTable :columns="columns" :rows="slots.data ?? []">
                    <template #cell-date="{ row }">{{ fmtDate(row.date) }}</template>
                    <template #cell-time="{ row }">{{ fmtTime(row.start_time) }} – {{ fmtTime(row.end_time) }}</template>
                    <template #cell-master="{ row }">{{ row.master?.name ?? 'Любой' }}</template>
                    <template #cell-status="{ value }">
                        <Badge :variant="statusVariant(value)">{{ statusLabel(value) }}</Badge>
                    </template>
                </DataTable>
            </div>
            <Pagination :links="slots.links ?? []" />
        </div>
    </CabinetLayout>
</template>
