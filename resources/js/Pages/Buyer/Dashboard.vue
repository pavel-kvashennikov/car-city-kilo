<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ShoppingCart, CalendarCheck, Heart, ArrowRight } from 'lucide-vue-next';

defineProps({
    ordersCount: { type: Number, default: 0 },
    appointmentsCount: { type: Number, default: 0 },
    favoritesCount: { type: Number, default: 0 },
});
</script>

<template>
    <AppLayout>
        <div class="container-app py-8">
            <div class="mb-6">
                <h1 class="page-title">Личный кабинет</h1>
                <p class="page-subtitle">Ваши заказы, записи и избранное</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Link href="/buyer/orders" class="card card-hover p-6 group">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center">
                            <ShoppingCart class="w-5 h-5 text-white" />
                        </div>
                        <ArrowRight class="w-4 h-4 text-on-surface-muted group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <p class="text-3xl font-extrabold text-on-surface mt-4">{{ ordersCount }}</p>
                    <p class="text-sm text-on-surface-muted">Заказы</p>
                </Link>

                <Link href="/buyer/appointments" class="card card-hover p-6 group">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center">
                            <CalendarCheck class="w-5 h-5 text-white" />
                        </div>
                        <ArrowRight class="w-4 h-4 text-on-surface-muted group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <p class="text-3xl font-extrabold text-on-surface mt-4">{{ appointmentsCount }}</p>
                    <p class="text-sm text-on-surface-muted">Записи на сервис</p>
                </Link>

                <Link href="/buyer/favorites" class="card card-hover p-6 group">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center">
                            <Heart class="w-5 h-5 text-white" />
                        </div>
                        <ArrowRight class="w-4 h-4 text-on-surface-muted group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <p class="text-3xl font-extrabold text-on-surface mt-4">{{ favoritesCount }}</p>
                    <p class="text-sm text-on-surface-muted">Избранное</p>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
