<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Phone, Mail, Car, Calendar, MessageSquare, CreditCard,
    ArrowLeftRight, PhoneCall, CheckCircle, Clock, XCircle, AlertCircle,
    ChevronDown, Inbox
} from 'lucide-vue-next';

const props = defineProps({
    leads:   { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
});

// ─── Status config ────────────────────────────────────────────────────────────
const statuses = [
    { value: '',           label: 'Все',        icon: null },
    { value: 'new',        label: 'Новые',      icon: AlertCircle },
    { value: 'in_progress',label: 'В работе',   icon: Clock },
    { value: 'completed',  label: 'Обработаны', icon: CheckCircle },
    { value: 'cancelled',  label: 'Отменены',   icon: XCircle },
];

const statusCfg = {
    new:         { label: 'Новый',      cls: 'badge-warning',  icon: AlertCircle },
    in_progress: { label: 'В работе',   cls: 'badge-primary',  icon: Clock },
    completed:   { label: 'Обработан',  cls: 'badge-success',  icon: CheckCircle },
    cancelled:   { label: 'Отменён',    cls: 'badge-neutral',  icon: XCircle },
};

const typeCfg = {
    test_drive: { label: 'Тест-драйв', icon: Car,          cls: 'text-primary' },
    credit:     { label: 'Кредит',     icon: CreditCard,   cls: 'text-warning' },
    trade_in:   { label: 'Trade-in',   icon: ArrowLeftRight,cls: 'text-tertiary' },
    callback:   { label: 'Звонок',     icon: PhoneCall,    cls: 'text-secondary' },
};

// ─── Filtering ────────────────────────────────────────────────────────────────
const activeStatus = ref(props.filters.status ?? '');

function applyFilter(status) {
    activeStatus.value = status;
    router.get('/cabinet/dealer/leads', { status: status || undefined }, { preserveScroll: true, replace: true });
}

// ─── Status update ────────────────────────────────────────────────────────────
const expandedNotes = ref({});
const notes = ref({});

function setStatus(lead, status) {
    router.put(`/cabinet/dealer/leads/${lead.id}`, { status, notes: notes.value[lead.id] ?? lead.notes ?? '' },
        { preserveScroll: true });
}

function toggleNotes(id) {
    expandedNotes.value[id] = !expandedNotes.value[id];
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function fmtDate(val) {
    if (!val) return null;
    return new Date(val).toLocaleString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function vehicleTitle(v) {
    if (!v) return null;
    return [v.brand?.name, v.car_model?.name, v.year].filter(Boolean).join(' ');
}

function vehiclePhoto(v) {
    if (!v?.photos?.length) return null;
    return `/storage/${v.photos[0].path}`;
}

function fmt(n) {
    return n ? Number(n).toLocaleString('ru-RU') + ' ₽' : null;
}
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div>
                <h1 class="page-title">Заявки от покупателей</h1>
                <p class="page-subtitle">Тест-драйвы, кредитные заявки, trade-in, обратные звонки</p>
            </div>

            <!-- Status tabs -->
            <div class="flex items-center gap-1 flex-wrap">
                <button
                    v-for="st in statuses" :key="st.value"
                    @click="applyFilter(st.value)"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors"
                    :class="activeStatus === st.value
                        ? 'bg-primary text-white'
                        : 'bg-surface-muted text-on-surface-muted hover:bg-surface-dim'"
                >
                    <component :is="st.icon" v-if="st.icon" class="w-3.5 h-3.5" />
                    {{ st.label }}
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="!leads.data?.length" class="card p-16 text-center">
                <Inbox class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">Заявок пока нет</p>
                <p class="text-sm text-on-surface-muted mt-1">
                    Они появятся когда покупатели оставят запросы на ваши автомобили
                </p>
            </div>

            <!-- Lead cards -->
            <div v-else class="space-y-4">
                <div v-for="lead in leads.data" :key="lead.id" class="card overflow-hidden">

                    <!-- Card header -->
                    <div class="flex items-start gap-4 p-5 pb-4">

                        <!-- Type icon -->
                        <div class="w-10 h-10 rounded-xl bg-surface-muted flex items-center justify-center shrink-0 mt-0.5">
                            <component
                                :is="typeCfg[lead.lead_type?.value ?? lead.lead_type]?.icon ?? PhoneCall"
                                class="w-5 h-5"
                                :class="typeCfg[lead.lead_type?.value ?? lead.lead_type]?.cls ?? 'text-secondary'"
                            />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <span class="font-bold text-sm text-on-surface">
                                    {{ typeCfg[lead.lead_type?.value ?? lead.lead_type]?.label ?? lead.lead_type }}
                                </span>
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                    :class="statusCfg[lead.status?.value ?? lead.status]?.cls ?? 'badge-neutral'">
                                    {{ statusCfg[lead.status?.value ?? lead.status]?.label ?? lead.status }}
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-muted">
                                {{ fmtDate(lead.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="px-5 pb-4 grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Client info -->
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-2">Клиент</p>
                            <p class="font-semibold text-on-surface">{{ lead.client_name }}</p>
                            <a v-if="lead.client_phone" :href="`tel:${lead.client_phone}`"
                                class="flex items-center gap-2 text-sm text-primary hover:underline">
                                <Phone class="w-3.5 h-3.5" /> {{ lead.client_phone }}
                            </a>
                            <a v-if="lead.client_email" :href="`mailto:${lead.client_email}`"
                                class="flex items-center gap-2 text-sm text-on-surface-muted hover:text-primary">
                                <Mail class="w-3.5 h-3.5" /> {{ lead.client_email }}
                            </a>
                            <div v-if="lead.preferred_datetime" class="flex items-center gap-2 text-sm text-on-surface-muted">
                                <Calendar class="w-3.5 h-3.5 text-primary shrink-0" />
                                Желаемое время: <span class="font-medium text-on-surface">{{ fmtDate(lead.preferred_datetime) }}</span>
                            </div>
                        </div>

                        <!-- Vehicle / extra data -->
                        <div class="space-y-3">
                            <!-- Vehicle -->
                            <div v-if="lead.vehicle">
                                <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-2">Автомобиль</p>
                                <Link :href="`/cabinet/dealer/vehicles/${lead.vehicle.id}/edit`"
                                    class="flex items-center gap-3 p-2.5 rounded-xl bg-surface-muted hover:bg-surface-dim transition-colors group">
                                    <div class="w-12 h-10 rounded-lg overflow-hidden bg-surface-dim shrink-0">
                                        <img v-if="vehiclePhoto(lead.vehicle)" :src="vehiclePhoto(lead.vehicle)"
                                            class="w-full h-full object-cover" />
                                        <div v-else class="w-full h-full flex items-center justify-center">
                                            <Car class="w-5 h-5 text-outline" />
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-sm text-on-surface truncate group-hover:text-primary">
                                            {{ vehicleTitle(lead.vehicle) }}
                                        </p>
                                        <p v-if="lead.vehicle.price" class="text-xs text-on-surface-muted">
                                            {{ fmt(lead.vehicle.price) }}
                                        </p>
                                    </div>
                                </Link>
                            </div>

                            <!-- Trade-in data -->
                            <div v-if="lead.trade_in_data && Object.keys(lead.trade_in_data).length">
                                <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-2">Trade-in автомобиль</p>
                                <div class="p-2.5 rounded-xl bg-surface-muted text-sm space-y-1">
                                    <p v-if="lead.trade_in_data.brand || lead.trade_in_data.model" class="font-medium">
                                        {{ [lead.trade_in_data.brand, lead.trade_in_data.model, lead.trade_in_data.year].filter(Boolean).join(' ') }}
                                    </p>
                                    <p v-if="lead.trade_in_data.mileage" class="text-on-surface-muted">
                                        Пробег: {{ Number(lead.trade_in_data.mileage).toLocaleString('ru-RU') }} км
                                    </p>
                                    <p v-if="lead.trade_in_data.condition" class="text-on-surface-muted">
                                        Состояние: {{ lead.trade_in_data.condition }}
                                    </p>
                                </div>
                            </div>

                            <!-- Credit data -->
                            <div v-if="lead.credit_data && Object.keys(lead.credit_data).length">
                                <p class="text-xs font-semibold uppercase tracking-wide text-on-surface-muted mb-2">Параметры кредита</p>
                                <div class="p-2.5 rounded-xl bg-surface-muted text-sm space-y-1">
                                    <p v-if="lead.credit_data.initial_payment" class="text-on-surface-muted">
                                        Первый взнос: <span class="font-medium text-on-surface">{{ fmt(lead.credit_data.initial_payment) }}</span>
                                    </p>
                                    <p v-if="lead.credit_data.term_months" class="text-on-surface-muted">
                                        Срок: <span class="font-medium text-on-surface">{{ lead.credit_data.term_months }} мес.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message -->
                    <div v-if="lead.message" class="mx-5 mb-4 p-3 rounded-lg bg-surface-muted/60 flex items-start gap-2">
                        <MessageSquare class="w-4 h-4 text-on-surface-muted mt-0.5 shrink-0" />
                        <p class="text-sm text-on-surface-muted italic">{{ lead.message }}</p>
                    </div>

                    <!-- Notes -->
                    <div class="mx-5 mb-4">
                        <button @click="toggleNotes(lead.id)"
                            class="flex items-center gap-1.5 text-xs text-on-surface-muted hover:text-primary mb-1.5">
                            <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': expandedNotes[lead.id] }" />
                            {{ lead.notes ? 'Заметки (есть)' : 'Добавить заметку' }}
                        </button>
                        <div v-if="expandedNotes[lead.id]" class="space-y-2">
                            <textarea
                                v-model="notes[lead.id]"
                                :placeholder="lead.notes || 'Внутренняя заметка по заявке...'"
                                rows="2"
                                class="input-field resize-none text-sm"
                            />
                        </div>
                        <p v-else-if="lead.notes && !expandedNotes[lead.id]"
                            class="text-sm text-on-surface-muted italic pl-5">{{ lead.notes }}</p>
                    </div>

                    <!-- Footer actions -->
                    <div class="border-t border-outline px-5 py-3 flex items-center justify-between gap-3 flex-wrap bg-surface-muted/30">
                        <div class="flex items-center gap-2">
                            <template v-if="(lead.status?.value ?? lead.status) === 'new'">
                                <button @click="setStatus(lead, 'in_progress')"
                                    class="btn-secondary !text-xs !py-1.5">
                                    <Clock class="w-3.5 h-3.5" /> В работу
                                </button>
                                <button @click="setStatus(lead, 'completed')"
                                    class="btn-primary !text-xs !py-1.5">
                                    <CheckCircle class="w-3.5 h-3.5" /> Обработан
                                </button>
                                <button @click="setStatus(lead, 'cancelled')"
                                    class="text-xs text-danger hover:underline px-2">
                                    Отменить
                                </button>
                            </template>
                            <template v-else-if="(lead.status?.value ?? lead.status) === 'in_progress'">
                                <button @click="setStatus(lead, 'completed')"
                                    class="btn-primary !text-xs !py-1.5">
                                    <CheckCircle class="w-3.5 h-3.5" /> Обработан
                                </button>
                                <button @click="setStatus(lead, 'cancelled')"
                                    class="text-xs text-danger hover:underline px-2">
                                    Отменить
                                </button>
                            </template>
                        </div>

                        <button v-if="expandedNotes[lead.id]"
                            @click="setStatus(lead, lead.status?.value ?? lead.status)"
                            class="text-xs font-semibold text-primary hover:underline">
                            Сохранить заметку
                        </button>
                    </div>
                </div>
            </div>

            <Pagination :links="leads.links" />
        </div>
    </CabinetLayout>
</template>
