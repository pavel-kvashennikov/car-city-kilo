<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'

defineProps({
    stats: { type: Object, default: () => ({}) },
})
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Админ-панель</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Компании</p>
                <p class="text-3xl font-bold mt-1">{{ stats.companies ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Пользователи</p>
                <p class="text-3xl font-bold mt-1">{{ stats.users ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">На модерации</p>
                <p class="text-3xl font-bold mt-1 text-orange-600">{{ stats.pending ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Заказы сегодня</p>
                <p class="text-3xl font-bold mt-1">{{ stats.orders_today ?? 0 }}</p>
            </div>
        </div>
    </AdminLayout>
</template>
