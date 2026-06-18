<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import DataTable from '@/Components/UI/DataTable.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Badge from '@/Components/UI/Badge.vue'
import { useForm } from '@inertiajs/vue3'
import Button from '@/Components/UI/Button.vue'
import Input from '@/Components/UI/Input.vue'

const props = defineProps({
    slots: { type: Object, default: () => ({ data: [], links: [] }) },
    masters: { type: Array, default: () => [] },
})

const form = useForm({
    date_from: '',
    date_to: '',
    start_time: '09:00',
    end_time: '18:00',
    slot_duration: 60,
    master_id: '',
})

const columns = [
    { key: 'date', label: 'Дата' },
    { key: 'time', label: 'Время' },
    { key: 'master', label: 'Мастер' },
    { key: 'status', label: 'Статус' },
]

const statusVariant = (status) => {
    const map = { available: 'success', booked: 'warning', blocked: 'danger' }
    return map[status] ?? 'default'
}

const generate = () => {
    form.post('/cabinet/service/schedule/slots')
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Расписание</h1>

        <form class="bg-white rounded-xl shadow p-6 mb-6 space-y-4" @submit.prevent="generate">
            <h2 class="font-semibold">Генерация слотов</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Input v-model="form.date_from" label="Дата с *" type="date" required />
                <Input v-model="form.date_to" label="Дата по *" type="date" required />
                <Input v-model="form.start_time" label="Начало" type="time" />
                <Input v-model="form.end_time" label="Конец" type="time" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.slot_duration" label="Длительность (мин)" type="number" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Мастер</label>
                    <select v-model="form.master_id" class="w-full rounded-lg border-gray-300">
                        <option value="">Любой</option>
                        <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>
            </div>
            <Button type="submit" :loading="form.processing">Создать слоты</Button>
        </form>

        <DataTable :columns="columns" :rows="slots.data ?? []">
            <template #cell-time="{ row }">
                {{ row.start_time }} – {{ row.end_time }}
            </template>
            <template #cell-master="{ row }">
                {{ row.master?.name ?? '—' }}
            </template>
            <template #cell-status="{ value }">
                <Badge :variant="statusVariant(value)">{{ value }}</Badge>
            </template>
        </DataTable>
        <div class="mt-6">
            <Pagination :links="slots.links ?? []" />
        </div>
    </CabinetLayout>
</template>
