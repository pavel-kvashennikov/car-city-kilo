<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    ordersCount: { type: Number, default: 0 },
    appointmentsCount: { type: Number, default: 0 },
    favoritesCount: { type: Number, default: 0 },
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Личный кабинет</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Link href="/buyer/orders" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
                    <p class="text-3xl font-bold text-blue-600">{{ ordersCount }}</p>
                    <p class="text-gray-600 mt-1">Заказы</p>
                </Link>
                <Link href="/buyer/appointments" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
                    <p class="text-3xl font-bold text-green-600">{{ appointmentsCount }}</p>
                    <p class="text-gray-600 mt-1">Записи на сервис</p>
                </Link>
                <div class="bg-white rounded-xl shadow p-6">
                    <p class="text-3xl font-bold text-yellow-600">{{ favoritesCount }}</p>
                    <p class="text-gray-600 mt-1">Избранное</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
