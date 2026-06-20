<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Building2, Car, Package, Wrench, Phone, Mail, Globe, AlertTriangle } from 'lucide-vue-next';

const props = defineProps({
    companies: { type: Object, default: () => ({ data: [], links: [] }) },
    filters:   { type: Object, default: () => ({}) },
    counts:    { type: Object, default: () => ({}) },
});

const search  = ref(props.filters.search ?? '');
const status  = ref(props.filters.status ?? '');
let timer = null;

watch([search, status], ([s, st]) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get('/admin/companies',
            { search: s || undefined, status: st || undefined },
            { preserveState: true, replace: true }
        );
    }, 350);
});

const STATUS = {
    active:    { label: 'Активна',       cls: 'badge-success' },
    pending:   { label: 'На проверке',   cls: 'badge-warning' },
    suspended: { label: 'Заблокирована', cls: 'badge-danger' },
    rejected:  { label: 'Отклонена',     cls: 'badge-neutral' },
};

const PROFILE_ICONS = { dealer: Car, parts: Package, service: Wrench };
const PROFILE_LABELS = { dealer: 'Дилер', parts: 'Запчасти', service: 'Сервис' };

// Reject modal
const rejectModal = ref(null); // { company }
const rejectReason = ref('');

const openReject = (company) => {
    rejectModal.value = company;
    rejectReason.value = '';
};
const confirmReject = () => {
    if (!rejectReason.value.trim()) return;
    router.put(`/admin/companies/${rejectModal.value.id}/reject`,
        { reason: rejectReason.value },
        { onSuccess: () => { rejectModal.value = null; } }
    );
};

const approve    = (id) => router.put(`/admin/companies/${id}/approve`, {}, { preserveScroll: true });
const suspend    = (id) => { if (confirm('Заблокировать компанию?')) router.put(`/admin/companies/${id}/suspend`, {}, { preserveScroll: true }); };
const reactivate = (id) => router.put(`/admin/companies/${id}/reactivate`, {}, { preserveScroll: true });

const TABS = [
    { key: '',          label: 'Все',            count: 'total' },
    { key: 'pending',   label: 'На проверке',    count: 'pending' },
    { key: 'active',    label: 'Активные',       count: 'active' },
    { key: 'suspended', label: 'Заблокированные',count: 'suspended' },
];

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="page-title">Компании</h1>
                    <p class="page-subtitle">Всего зарегистрировано: {{ counts.total ?? 0 }}</p>
                </div>
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-muted" />
                    <input v-model="search" placeholder="Поиск по названию, ИНН, email..."
                        class="input-field !pl-9 !text-sm w-72" />
                </div>
            </div>

            <!-- Status tabs -->
            <div class="flex items-center gap-1 overflow-x-auto pb-1">
                <button v-for="tab in TABS" :key="tab.key"
                    @click="status = tab.key"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap transition flex items-center gap-1.5"
                    :class="status === tab.key
                        ? 'bg-primary text-white'
                        : 'text-on-surface-muted hover:bg-surface-muted'">
                    {{ tab.label }}
                    <span v-if="counts[tab.count]"
                        class="text-xs px-1.5 py-0.5 rounded-full"
                        :class="status === tab.key ? 'bg-white/20' : 'bg-surface-muted'">
                        {{ counts[tab.count] }}
                    </span>
                </button>
            </div>

            <!-- Empty -->
            <div v-if="!companies.data?.length" class="card p-16 text-center">
                <Building2 class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted">Компании не найдены</p>
            </div>

            <!-- Cards -->
            <div v-else class="space-y-3">
                <div v-for="co in companies.data" :key="co.id" class="card p-5">
                    <div class="flex items-start gap-4">

                        <!-- Logo / Avatar -->
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-surface-muted shrink-0 flex items-center justify-center">
                            <img v-if="co.logo_path" :src="`/storage/${co.logo_path}`"
                                class="w-full h-full object-cover" />
                            <Building2 v-else class="w-6 h-6 text-outline" />
                        </div>

                        <!-- Main info -->
                        <div class="flex-1 min-w-0 space-y-2">
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div>
                                    <h3 class="font-semibold text-on-surface">{{ co.name }}</h3>
                                    <p v-if="co.legal_name && co.legal_name !== co.name"
                                        class="text-xs text-on-surface-muted">{{ co.legal_name }}</p>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                                    :class="STATUS[co.status?.value ?? co.status]?.cls ?? 'badge-neutral'">
                                    {{ STATUS[co.status?.value ?? co.status]?.label ?? co.status }}
                                </span>
                            </div>

                            <!-- Details row -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-on-surface-muted">
                                <span v-if="co.inn" class="font-mono">ИНН: {{ co.inn }}</span>
                                <span v-if="co.owner">
                                    Владелец: <strong class="text-on-surface">{{ co.owner.name }}</strong>
                                    <span class="ml-1 text-on-surface-muted">({{ co.owner.email }})</span>
                                </span>
                                <span>Зарегистрирована: {{ fmtDate(co.created_at) }}</span>
                                <span v-if="co.verified_at" class="text-success">
                                    Верифицирована: {{ fmtDate(co.verified_at) }}
                                </span>
                            </div>

                            <!-- Contacts -->
                            <div class="flex flex-wrap items-center gap-3 text-xs text-on-surface-muted">
                                <a v-if="co.phone" :href="`tel:${co.phone}`"
                                    class="flex items-center gap-1 hover:text-primary transition-colors">
                                    <Phone class="w-3.5 h-3.5" /> {{ co.phone }}
                                </a>
                                <a v-if="co.email" :href="`mailto:${co.email}`"
                                    class="flex items-center gap-1 hover:text-primary transition-colors">
                                    <Mail class="w-3.5 h-3.5" /> {{ co.email }}
                                </a>
                                <a v-if="co.website" :href="co.website" target="_blank"
                                    class="flex items-center gap-1 hover:text-primary transition-colors">
                                    <Globe class="w-3.5 h-3.5" /> {{ co.website }}
                                </a>
                            </div>

                            <!-- Business profiles -->
                            <div v-if="co.business_profiles?.length" class="flex flex-wrap gap-1.5">
                                <span v-for="p in co.business_profiles" :key="p.id"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-surface-muted text-xs font-medium text-on-surface">
                                    <component :is="PROFILE_ICONS[p.type]" class="w-3 h-3" />
                                    {{ PROFILE_LABELS[p.type] ?? p.type }}
                                </span>
                            </div>

                            <!-- Reject reason -->
                            <div v-if="co.reject_reason"
                                class="flex items-start gap-2 text-xs text-danger bg-danger/5 rounded-lg px-3 py-2">
                                <AlertTriangle class="w-3.5 h-3.5 mt-0.5 shrink-0" />
                                <span>Причина отклонения: {{ co.reject_reason }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-2 shrink-0 items-end">
                            <template v-if="(co.status?.value ?? co.status) === 'pending'">
                                <button @click="approve(co.id)"
                                    class="btn-success !text-xs !py-1.5 !px-3">
                                    Одобрить
                                </button>
                                <button @click="openReject(co)"
                                    class="btn-danger !text-xs !py-1.5 !px-3">
                                    Отклонить
                                </button>
                            </template>
                            <template v-else-if="(co.status?.value ?? co.status) === 'active'">
                                <button @click="suspend(co.id)"
                                    class="text-xs text-danger hover:underline">
                                    Заблокировать
                                </button>
                            </template>
                            <template v-else-if="['suspended','rejected'].includes(co.status?.value ?? co.status)">
                                <button @click="reactivate(co.id)"
                                    class="btn-primary !text-xs !py-1.5 !px-3">
                                    Восстановить
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <Pagination :links="companies.links ?? []" />
        </div>

        <!-- Reject modal -->
        <Teleport to="body">
            <div v-if="rejectModal"
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                @click.self="rejectModal = null">
                <div class="card p-6 w-full max-w-md space-y-4">
                    <h3 class="font-bold text-on-surface">Отклонить компанию</h3>
                    <p class="text-sm text-on-surface-muted">
                        Укажите причину отклонения для <strong>{{ rejectModal.name }}</strong>. Она будет показана владельцу.
                    </p>
                    <textarea v-model="rejectReason" rows="3"
                        class="input-field w-full resize-none text-sm"
                        placeholder="Например: Неверно указан ИНН, недостаточно данных..."></textarea>
                    <div class="flex gap-3">
                        <button @click="confirmReject"
                            :disabled="!rejectReason.trim()"
                            class="btn-danger !text-sm flex-1 disabled:opacity-50">
                            Отклонить
                        </button>
                        <button @click="rejectModal = null" class="btn-secondary !text-sm">
                            Отмена
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
