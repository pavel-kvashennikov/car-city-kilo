<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Banknote, Car, Phone, User } from 'lucide-vue-next';

const props = defineProps({
    inquiries: { type: Object, default: () => ({ data: [], links: [] }) },
    counts:    { type: Object, default: () => ({}) },
    filters:   { type: Object, default: () => ({}) },
});

const status = ref(props.filters.status ?? '');

watch(status, (val) => {
    router.get('/cabinet/dealer/sell-car-inquiries',
        { status: val || undefined },
        { preserveState: true, replace: true }
    );
});

const TABS = [
    { key: '',            label: 'Все',         countKey: 'total' },
    { key: 'new',         label: 'Новые',       countKey: 'new' },
    { key: 'in_progress', label: 'В работе',    countKey: 'in_progress' },
    { key: 'done',        label: 'Завершены',   countKey: 'done' },
];

const STATUS = {
    new:         { label: 'Новая',     cls: 'badge-warning' },
    in_progress: { label: 'В работе',  cls: 'badge-primary' },
    done:        { label: 'Завершена', cls: 'badge-success' },
};

const STATUS_OPTIONS = [
    { value: 'new',         label: 'Новая' },
    { value: 'in_progress', label: 'В работе' },
    { value: 'done',        label: 'Завершена' },
];

function changeStatus(inquiry, newStatus) {
    router.put(`/cabinet/dealer/sell-car-inquiries/${inquiry.id}`,
        { status: newStatus },
        { preserveScroll: true }
    );
}

const fmtDate = (d) => d
    ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    : '—';

const fmtNum = (n) => n ? new Intl.NumberFormat('ru-RU').format(n) : '—';

</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="page-title">Заявки на выкуп</h1>
                    <p class="page-subtitle">Клиенты, желающие продать автомобиль — {{ counts.total ?? 0 }} всего</p>
                </div>
            </div>

            <!-- Tabs / filters -->
            <div class="flex gap-1 flex-wrap">
                <button
                    v-for="tab in TABS"
                    :key="tab.key"
                    @click="status = tab.key"
                    :class="[
                        'px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors',
                        status === tab.key
                            ? 'bg-primary text-white shadow-sm'
                            : 'bg-surface-muted text-on-surface-muted hover:bg-surface-dim'
                    ]"
                >
                    {{ tab.label }}
                    <span class="ml-1 opacity-70">({{ counts[tab.countKey] ?? 0 }})</span>
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="!inquiries.data.length" class="card p-12 text-center">
                <Banknote class="w-10 h-10 text-outline mx-auto mb-3" />
                <p class="font-semibold text-on-surface">Заявок пока нет</p>
                <p class="text-sm text-on-surface-muted mt-1">
                    Когда клиенты оставят заявки на странице «Продать авто», они появятся здесь.
                </p>
            </div>

            <!-- Table -->
            <div v-else class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-outline bg-surface-muted text-xs text-on-surface-muted uppercase tracking-wide">
                                <th class="px-4 py-3 text-left font-semibold">Автомобиль</th>
                                <th class="px-4 py-3 text-left font-semibold">Клиент</th>
                                <th class="px-4 py-3 text-left font-semibold hidden sm:table-cell">Дата</th>
                                <th class="px-4 py-3 text-left font-semibold">Статус</th>
                                <th class="px-4 py-3 text-right font-semibold">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline">
                            <tr
                                v-for="item in inquiries.data"
                                :key="item.id"
                                class="hover:bg-surface-muted/50 transition-colors"
                            >
                                <!-- Car info -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-primary-light flex items-center justify-center shrink-0">
                                            <Car class="w-4 h-4 text-primary" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-on-surface truncate max-w-[180px]">{{ item.car_info }}</p>
                                            <p class="text-xs text-on-surface-muted mt-0.5 flex gap-2">
                                                <span v-if="item.year">{{ item.year }} г.</span>
                                                <span v-if="item.mileage">{{ fmtNum(item.mileage) }} км</span>
                                                <span v-if="!item.year && !item.mileage" class="italic">Без деталей</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Client -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <Phone class="w-3.5 h-3.5 text-on-surface-muted shrink-0" />
                                        <span class="font-medium text-on-surface">{{ item.client_phone }}</span>
                                    </div>
                                    <div v-if="item.user" class="flex items-center gap-1.5 mt-1">
                                        <User class="w-3 h-3 text-on-surface-muted" />
                                        <span class="text-xs text-on-surface-muted">{{ item.user.name }}</span>
                                    </div>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3.5 text-xs text-on-surface-muted hidden sm:table-cell whitespace-nowrap">
                                    {{ fmtDate(item.created_at) }}
                                </td>

                                <!-- Status badge -->
                                <td class="px-4 py-3.5">
                                    <span :class="['badge', STATUS[item.status]?.cls ?? 'badge-neutral']">
                                        {{ STATUS[item.status]?.label ?? item.status }}
                                    </span>
                                </td>

                                <!-- Status select -->
                                <td class="px-4 py-3.5 text-right">
                                    <select
                                        :value="item.status"
                                        @change="changeStatus(item, $event.target.value)"
                                        class="text-xs rounded-lg border border-outline bg-surface px-2.5 py-1.5 text-on-surface cursor-pointer outline-none focus:border-primary transition-colors"
                                    >
                                        <option v-for="opt in STATUS_OPTIONS" :key="opt.value" :value="opt.value">
                                            {{ opt.label }}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination v-if="inquiries.links?.length > 3" :links="inquiries.links" />

        </div>
    </CabinetLayout>
</template>
