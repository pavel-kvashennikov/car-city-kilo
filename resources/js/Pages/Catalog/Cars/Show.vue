<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    vehicle: Object,
    similar: Array,
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Gallery -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border overflow-hidden">
                        <div class="aspect-[16/10] bg-gray-100">
                            <img
                                v-if="vehicle.photos?.[0]"
                                :src="`/storage/${vehicle.photos[0].path}`"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-lg">
                                Нет фотографий
                            </div>
                        </div>
                        <div v-if="vehicle.photos?.length > 1" class="flex gap-2 p-4 overflow-x-auto">
                            <img
                                v-for="photo in vehicle.photos"
                                :key="photo.id"
                                :src="`/storage/${photo.path}`"
                                class="w-20 h-16 rounded object-cover cursor-pointer border-2 border-transparent hover:border-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Specs -->
                    <div class="bg-white rounded-xl border mt-6 p-6">
                        <h2 class="text-xl font-bold mb-4">Характеристики</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Год</span><span>{{ vehicle.year }}</span></div>
                            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Пробег</span><span>{{ vehicle.mileage?.toLocaleString() }} км</span></div>
                            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Двигатель</span><span>{{ vehicle.engine_volume }}л / {{ vehicle.engine_power }} л.с.</span></div>
                            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">КПП</span><span>{{ vehicle.transmission }}</span></div>
                            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Привод</span><span>{{ vehicle.drive_type }}</span></div>
                            <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Цвет</span><span>{{ vehicle.color }}</span></div>
                            <div v-if="vehicle.vin" class="flex justify-between py-2 border-b"><span class="text-gray-500">VIN</span><span>{{ vehicle.vin }}</span></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="vehicle.description" class="bg-white rounded-xl border mt-6 p-6">
                        <h2 class="text-xl font-bold mb-4">Описание</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ vehicle.description }}</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border p-6 sticky top-24">
                        <h1 class="text-xl font-bold">{{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ vehicle.year }}, {{ vehicle.mileage?.toLocaleString() }} км</p>
                        <div class="text-3xl font-bold text-blue-600 mt-4">{{ Number(vehicle.price).toLocaleString() }} ₽</div>

                        <div v-if="vehicle.dealer_profile?.company" class="mt-6 pt-6 border-t">
                            <p class="text-sm text-gray-500">Продавец</p>
                            <p class="font-semibold">{{ vehicle.dealer_profile.company.name }}</p>
                        </div>

                        <div class="mt-6 space-y-3">
                            <button class="w-full py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                                Позвонить
                            </button>
                            <button class="w-full py-3 border border-blue-600 text-blue-600 rounded-lg font-medium hover:bg-blue-50">
                                Записаться на тест-драйв
                            </button>
                            <button class="w-full py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                                Рассчитать кредит
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
