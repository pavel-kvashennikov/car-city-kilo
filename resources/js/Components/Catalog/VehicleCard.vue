<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    vehicle: { type: Object, required: true },
})

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price) + ' ₽'
</script>

<template>
    <Link :href="`/cars/${vehicle.slug}`" class="block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <div class="aspect-[4/3] bg-gray-100">
            <img
                v-if="vehicle.photos?.[0]?.path"
                :src="vehicle.photos[0].path"
                :alt="vehicle.slug"
                class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                Нет фото
            </div>
        </div>
        <div class="p-4">
            <h3 class="font-semibold text-gray-900 truncate">
                {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                {{ vehicle.year }} · {{ vehicle.mileage?.toLocaleString() }} км
            </p>
            <p class="text-lg font-bold text-blue-600 mt-2">
                {{ formatPrice(vehicle.price) }}
            </p>
        </div>
    </Link>
</template>
