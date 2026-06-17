<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'

defineProps({
    stats: { type: Object, default: () => ({}) },
    company: { type: Object, default: () => ({}) },
})
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Дашборд</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Просмотры сегодня</p>
                <p class="text-3xl font-bold mt-1">{{ stats.views_today ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Лиды за неделю</p>
                <p class="text-3xl font-bold mt-1">{{ stats.leads_week ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Заказы за месяц</p>
                <p class="text-3xl font-bold mt-1">{{ stats.orders_month ?? 0 }}</p>
            </div>
        </div>
    </CabinetLayout>
</template>
