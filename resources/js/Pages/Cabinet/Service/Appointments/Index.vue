<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { CalendarDays, Phone, User, Wrench, UserCheck, MessageSquare, Clock, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    appointments: { type: Object, default: () => ({ data: [], links: [] }) },
});

const STATUS_CFG = {
    pending:     { label: 'Ожидает',     cls: 'badge-warning',   next: ['confirmed','cancelled'] },
    confirmed:   { label: 'Подтверждена',cls: 'badge-info',      next: ['in_progress','cancelled'] },
    in_progress: { label: 'В работе',    cls: 'badge-primary',   next: ['completed','no_show'] },
    completed:   { label: 'Завершена',   cls: 'badge-success',   next: [] },
    cancelled:   { label: 'Отменена',    cls: 'badge-neutral',   next: [] },
    no_show:     { label: 'Не явился',   cls: 'badge-danger',    next: [] },
};

const ACTION_LABELS = {
    confirmed:   'Подтвердить',
    in_progress: 'В работу',
    completed:   'Завершить',
    cancelled:   'Отменить',
    no_show:     'Не явился',
};

const ACTION_CLS = {
    confirmed:   'btn-primary',
    in_progress: 'btn-secondary',
    completed:   'btn-success',
    cancelled:   'text-danger hover:underline text-sm',
    no_show:     'text-on-surface-muted hover:underline text-sm',
};

const ALL_TABS = [
    { key: '',            label: 'Все' },
    { key: 'pending',     label: 'Ожидают' },
    { key: 'confirmed',   label: 'Подтверждены' },
    { key: 'in_progress', label: 'В работе' },
    { key: 'completed',   label: 'Завершены' },
    { key: 'cancelled',   label: 'Отменены' },
];

const activeTab   = ref('');
const notesOpen   = ref(null);
const notesText   = ref('');

const filterTab = (key) => {
    activeTab.value = key;
    router.get('/cabinet/service/appointments', key ? { status: key } : {}, { preserveState: true, replace: true });
};

const fmtDate = (d) => {
    if (!d) return '—';
    const iso = String(d).includes('T') ? d : d + 'T00:00:00';
    const date = new Date(iso);
    return isNaN(date) ? d : date.toLocaleDateString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric' });
};
const fmtTime = (t) => t ? String(t).slice(0, 5) : '';

const setStatus = (id, status) => {
    router.patch(`/cabinet/service/appointments/${id}`, { status }, { preserveState: false });
};

const openNotes = (appt) => {
    notesOpen.value = appt.id;
    notesText.value = appt.internal_notes ?? '';
};

const saveNotes = (id) => {
    router.patch(`/cabinet/service/appointments/${id}`, { internal_notes: notesText.value }, {
        preserveState: false,
        onSuccess: () => { notesOpen.value = null; },
    });
};

const destroy = (id) => {
    if (confirm('Удалить запись?'))
        router.delete(`/cabinet/service/appointments/${id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div>
                <h1 class="page-title">Записи на сервис</h1>
                <p class="page-subtitle">Управление заявками клиентов</p>
            </div>

            <!-- Status tabs -->
            <div class="flex items-center gap-1 overflow-x-auto pb-1">
                <button v-for="tab in ALL_TABS" :key="tab.key"
                    @click="filterTab(tab.key)"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap transition"
                    :class="activeTab === tab.key
                        ? 'bg-primary text-white'
                        : 'text-on-surface-muted hover:bg-surface-muted'">
                    {{ tab.label }}
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="!appointments.data?.length" class="card p-16 text-center">
                <CalendarDays class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">Записей нет</p>
                <p class="text-sm text-on-surface-muted mt-1">Клиенты ещё не оставляли заявок на сервис</p>
            </div>

            <!-- Cards -->
            <div v-else class="space-y-4">
                <div v-for="appt in appointments.data" :key="appt.id" class="card p-5 space-y-4">

                    <!-- Top row -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                <CalendarDays class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-on-surface">
                                    {{ fmtDate(appt.time_slot?.date) }}
                                    <span v-if="appt.time_slot" class="text-on-surface-muted font-normal ml-1">
                                        {{ fmtTime(appt.time_slot.start_time) }} – {{ fmtTime(appt.time_slot.end_time) }}
                                    </span>
                                    <span v-else class="text-on-surface-muted font-normal ml-1 text-xs">без слота</span>
                                </p>
                                <p class="text-xs text-on-surface-muted mt-0.5">
                                    Заявка #{{ appt.id }} · {{ fmtDate(appt.created_at) }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                            :class="STATUS_CFG[appt.status]?.cls ?? 'badge-neutral'">
                            {{ STATUS_CFG[appt.status]?.label ?? appt.status }}
                        </span>
                    </div>

                    <!-- Details grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">

                        <!-- Client -->
                        <div class="flex items-start gap-2">
                            <User class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                            <div>
                                <p class="text-xs text-on-surface-muted">Клиент</p>
                                <p class="font-medium text-on-surface">{{ appt.contact_name || appt.user?.name || '—' }}</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-2">
                            <Phone class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                            <div>
                                <p class="text-xs text-on-surface-muted">Телефон</p>
                                <a v-if="appt.contact_phone" :href="`tel:${appt.contact_phone}`"
                                    class="font-medium text-primary hover:underline">
                                    {{ appt.contact_phone }}
                                </a>
                                <p v-else class="text-on-surface-muted">—</p>
                            </div>
                        </div>

                        <!-- Service -->
                        <div class="flex items-start gap-2">
                            <Wrench class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                            <div>
                                <p class="text-xs text-on-surface-muted">Услуга</p>
                                <p class="font-medium text-on-surface">{{ appt.service_item?.name ?? '—' }}</p>
                            </div>
                        </div>

                        <!-- Master -->
                        <div class="flex items-start gap-2">
                            <UserCheck class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                            <div>
                                <p class="text-xs text-on-surface-muted">Мастер</p>
                                <p class="font-medium text-on-surface">{{ appt.master?.name ?? 'Любой' }}</p>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div v-if="appt.service_item?.duration_minutes" class="flex items-start gap-2">
                            <Clock class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                            <div>
                                <p class="text-xs text-on-surface-muted">Длительность</p>
                                <p class="font-medium text-on-surface">{{ appt.service_item.duration_minutes }} мин</p>
                            </div>
                        </div>

                        <!-- Client comment -->
                        <div v-if="appt.notes" class="flex items-start gap-2 sm:col-span-2 lg:col-span-1">
                            <MessageSquare class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                            <div>
                                <p class="text-xs text-on-surface-muted">Комментарий клиента</p>
                                <p class="text-on-surface">{{ appt.notes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Internal notes -->
                    <div v-if="notesOpen === appt.id" class="border-t border-outline pt-3 space-y-2">
                        <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wide">Внутренние заметки</p>
                        <textarea v-model="notesText" rows="2"
                            class="input-field w-full resize-none text-sm"
                            placeholder="Заметки для внутреннего использования..."></textarea>
                        <div class="flex gap-2">
                            <button @click="saveNotes(appt.id)" class="btn-primary !text-xs !py-1.5">Сохранить</button>
                            <button @click="notesOpen = null" class="btn-secondary !text-xs !py-1.5">Отмена</button>
                        </div>
                    </div>
                    <div v-else-if="appt.internal_notes" class="border-t border-outline pt-3">
                        <p class="text-xs text-on-surface-muted font-semibold mb-1">Заметки:</p>
                        <p class="text-sm text-on-surface">{{ appt.internal_notes }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-outline pt-3 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <template v-for="nextStatus in (STATUS_CFG[appt.status]?.next ?? [])" :key="nextStatus">
                                <button @click="setStatus(appt.id, nextStatus)"
                                    class="!text-xs !py-1.5 !px-3"
                                    :class="ACTION_CLS[nextStatus] ?? 'btn-secondary'">
                                    {{ ACTION_LABELS[nextStatus] }}
                                </button>
                            </template>
                            <button @click="openNotes(appt)"
                                class="btn-secondary !text-xs !py-1.5 !px-3">
                                Заметки
                            </button>
                        </div>
                        <button @click="destroy(appt.id)"
                            class="inline-flex items-center gap-1 text-xs text-on-surface-muted hover:text-danger transition-colors">
                            <Trash2 class="w-3.5 h-3.5" /> Удалить
                        </button>
                    </div>
                </div>
            </div>

            <Pagination :links="appointments.links ?? []" />
        </div>
    </CabinetLayout>
</template>
