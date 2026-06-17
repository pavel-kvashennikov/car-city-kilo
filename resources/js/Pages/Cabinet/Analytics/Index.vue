<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'

defineProps({
    stats: { type: Object, default: () => ({}) },
})
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Аналитика</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Просмотры</p>
                <p class="text-3xl font-bold mt-1">{{ stats.views ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Лиды</p>
                <p class="text-3xl font-bold mt-1">{{ stats.leads ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Заказы</p>
                <p class="text-3xl font-bold mt-1">{{ stats.orders ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Конверсия</p>
                <p class="text-3xl font-bold mt-1">{{ stats.conversion ?? '0%' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-500 text-center py-8">Графики аналитики будут доступны после накопления данных</p>
        </div>
    </CabinetLayout>
</template>
