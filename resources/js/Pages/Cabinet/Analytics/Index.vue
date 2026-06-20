<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import { Eye, ClipboardList, ShoppingCart, TrendingUp, BarChart2 } from 'lucide-vue-next';

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
});

const cards = [
    { label: 'Просмотры', key: 'views', icon: Eye, color: 'from-blue-500 to-blue-700' },
    { label: 'Лиды', key: 'leads', icon: ClipboardList, color: 'from-violet-500 to-violet-700' },
    { label: 'Заказы', key: 'orders', icon: ShoppingCart, color: 'from-teal-500 to-teal-700' },
    { label: 'Конверсия', key: 'conversion', icon: TrendingUp, color: 'from-amber-500 to-orange-600' },
];
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6">
            <div>
                <h1 class="page-title">Аналитика</h1>
                <p class="page-subtitle">Показатели вашей компании</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="c in cards" :key="c.key" class="card p-5 flex items-center gap-4">
                    <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center shrink-0', c.color]">
                        <component :is="c.icon" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-on-surface">{{ stats[c.key] ?? (c.key === 'conversion' ? '0%' : 0) }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">{{ c.label }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-10 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-xl bg-surface-muted flex items-center justify-center mb-3">
                    <BarChart2 class="w-6 h-6 text-on-surface-muted" />
                </div>
                <p class="font-semibold text-on-surface">Графики скоро появятся</p>
                <p class="text-sm text-on-surface-muted mt-1 max-w-sm">Детальная аналитика с графиками станет доступна после накопления данных по вашей компании.</p>
            </div>
        </div>
    </CabinetLayout>
</template>
