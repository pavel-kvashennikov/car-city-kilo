<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'

defineProps({
    stats: { type: Object, default: () => ({}) },
})
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Аналитика</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Всего пользователей</p>
                <p class="text-3xl font-bold mt-1">{{ stats.total_users ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Компании</p>
                <p class="text-3xl font-bold mt-1">{{ stats.total_companies ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Заказов за месяц</p>
                <p class="text-3xl font-bold mt-1">{{ stats.orders_month ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Выручка</p>
                <p class="text-3xl font-bold mt-1">{{ stats.revenue ?? '0 ₽' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-500 text-center py-8">Детальная аналитика и графики</p>
        </div>
    </AdminLayout>
</template>
