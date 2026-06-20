<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CalendarPlus, Calendar, Lock, Unlock, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    slots:   { type: Object, default: () => ({ data: [], links: [] }) },
    masters: { type: Array,  default: () => [] },
});

const form = useForm({
    date_from:     '',
    date_to:       '',
    start_time:    '09:00',
    end_time:      '18:00',
    slot_duration: 60,
    master_id:     '',
});

const filterStatus = ref('');
const filterMaster = ref('');

const STATUS_CFG = {
    available: { label: 'Свободен',    cls: 'badge-success' },
    booked:    { label: 'Занят',       cls: 'badge-warning' },
    blocked:   { label: 'Заблокирован',cls: 'badge-neutral' },
};

const fmtDate = (d) => {
    if (!d) return '—';
    const iso = String(d).includes('T') ? d : d + 'T00:00:00';
    const date = new Date(iso);
    return isNaN(date) ? d : date.toLocaleDateString('ru-RU', { day: '2-digit', month: 'short', year: 'numeric' });
};
const fmtTime = (t) => t ? String(t).slice(0, 5) : '';

const generate = () => form.post('/cabinet/service/schedule/slots');

const applyFilter = () => {
    const params = {};
    if (filterStatus.value) params.status = filterStatus.value;
    if (filterMaster.value) params.master_id = filterMaster.value;
    router.get('/cabinet/service/schedule', params, { preserveState: true, replace: true });
};

const toggleBlock = (slot) => {
    const newStatus = slot.status === 'blocked' ? 'available' : 'blocked';
    router.patch(`/cabinet/service/schedule/slots/${slot.id}`, { status: newStatus });
};

const destroySlot = (slot) => {
    if (slot.status === 'booked') return;
    if (confirm('Удалить этот слот?'))
        router.delete(`/cabinet/service/schedule/slots/${slot.id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6">

            <div>
                <h1 class="page-title">Расписание</h1>
                <p class="page-subtitle">Генерация слотов для онлайн-записи клиентов</p>
            </div>

            <!-- Generate form -->
            <form class="card p-5 space-y-4" @submit.prevent="generate">
                <h2 class="font-semibold text-on-surface flex items-center gap-2 border-b border-outline pb-3">
                    <CalendarPlus class="w-4 h-4 text-primary" /> Создать слоты
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <Input v-model="form.date_from" label="Дата с" type="date" required :error="form.errors.date_from" />
                    <Input v-model="form.date_to"   label="Дата по" type="date" required :error="form.errors.date_to" />
                    <Input v-model="form.start_time" label="Начало рабочего дня" type="time" />
                    <Input v-model="form.end_time"   label="Конец рабочего дня"  type="time" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <Input v-model="form.slot_duration" label="Длительность слота, мин" type="number" min="15" max="480" />
                    <Select v-model="form.master_id" label="Мастер (необязательно)"
                        placeholder="Без привязки к мастеру"
                        :options="masters" option-value="id" option-label="name" />
                </div>

                <div class="pt-2 border-t border-outline flex items-center gap-3">
                    <Button type="submit" :loading="form.processing">Создать слоты</Button>
                    <span class="text-xs text-on-surface-muted">Дублирующие слоты создаваться не будут</span>
                </div>
            </form>

            <!-- Filter bar -->
            <div class="card p-4 flex items-end gap-3 flex-wrap">
                <div class="flex-1 min-w-36">
                    <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Статус</label>
                    <select v-model="filterStatus" @change="applyFilter" class="input-field !py-1.5 !text-sm w-full">
                        <option value="">Все</option>
                        <option value="available">Свободные</option>
                        <option value="booked">Занятые</option>
                        <option value="blocked">Заблокированные</option>
                    </select>
                </div>
                <div class="flex-1 min-w-36">
                    <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Мастер</label>
                    <select v-model="filterMaster" @change="applyFilter" class="input-field !py-1.5 !text-sm w-full">
                        <option value="">Все</option>
                        <option v-for="m in masters" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Slots list -->
            <div v-if="!slots.data?.length" class="card p-16 text-center">
                <Calendar class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">Слоты не созданы</p>
                <p class="text-sm text-on-surface-muted mt-1">Используйте форму выше для генерации расписания</p>
            </div>

            <div v-else class="card overflow-hidden">
                <div class="divide-y divide-outline">
                    <div v-for="slot in slots.data" :key="slot.id"
                        class="flex items-center gap-3 px-5 py-3 hover:bg-surface-muted/40 transition-colors"
                        :class="{ 'opacity-50': slot.status === 'blocked' }">

                        <!-- Date & Time -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-on-surface">
                                {{ fmtDate(slot.date) }}
                                <span class="font-normal text-on-surface-muted ml-1">
                                    {{ fmtTime(slot.start_time) }} – {{ fmtTime(slot.end_time) }}
                                </span>
                            </p>
                            <p v-if="slot.master" class="text-xs text-on-surface-muted mt-0.5">{{ slot.master.name }}</p>
                        </div>

                        <!-- Status badge -->
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                            :class="STATUS_CFG[slot.status]?.cls ?? 'badge-neutral'">
                            {{ STATUS_CFG[slot.status]?.label ?? slot.status }}
                        </span>

                        <!-- Actions -->
                        <div v-if="slot.status !== 'booked'" class="flex items-center gap-1 shrink-0">
                            <button @click="toggleBlock(slot)"
                                class="p-1.5 rounded-lg hover:bg-surface-muted transition-colors"
                                :title="slot.status === 'blocked' ? 'Разблокировать' : 'Заблокировать'">
                                <Lock v-if="slot.status !== 'blocked'" class="w-4 h-4 text-on-surface-muted" />
                                <Unlock v-else class="w-4 h-4 text-primary" />
                            </button>
                            <button @click="destroySlot(slot)"
                                class="p-1.5 rounded-lg hover:bg-surface-muted transition-colors"
                                title="Удалить слот">
                                <Trash2 class="w-4 h-4 text-on-surface-muted hover:text-danger" />
                            </button>
                        </div>
                        <div v-else class="w-16 shrink-0" />
                    </div>
                </div>
            </div>

            <Pagination :links="slots.links ?? []" />
        </div>
    </CabinetLayout>
</template>
