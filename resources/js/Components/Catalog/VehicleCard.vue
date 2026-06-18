<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    vehicle: { type: Object, required: true },
})

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price) + ' ₽'
</script>

<template>
    <Link
        :href="`/catalog/cars/${vehicle.slug}`"
        class="card card-hover overflow-hidden group block"
    >
        <div class="aspect-[4/3] bg-surface-muted relative overflow-hidden">
            <img
                v-if="vehicle.photos?.[0]?.path"
                :src="vehicle.photos[0].path"
                :alt="vehicle.slug"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-secondary">
                <span class="text-sm">Нет фото</span>
            </div>
            <div class="absolute top-3 left-3">
                <span class="badge badge-primary">{{ vehicle.year }}</span>
            </div>
        </div>
        <div class="p-5">
            <h3 class="font-semibold text-on-surface truncate group-hover:text-primary transition-colors">
                {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
            </h3>
            <p class="text-sm text-on-surface-muted mt-1.5">
                {{ vehicle.mileage?.toLocaleString('ru-RU') }} км
                <span v-if="vehicle.transmission"> · {{ vehicle.transmission }}</span>
            </p>
            <p class="text-xl font-bold text-gradient mt-3">
                {{ formatPrice(vehicle.price) }}
            </p>
        </div>
    </Link>
</template>
