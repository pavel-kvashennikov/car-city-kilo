<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import VehicleCard from '@/Components/Catalog/VehicleCard.vue'
import ProductCard from '@/Components/Catalog/ProductCard.vue'

defineProps({
    company: { type: Object, required: true },
    vehicles: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
})
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <div class="flex items-start gap-6">
                    <div class="w-20 h-20 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                        <span class="text-blue-600 font-bold text-3xl">{{ company.name?.charAt(0) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ company.name }}</h1>
                        <p v-if="company.description" class="text-gray-600 mt-2">{{ company.description }}</p>
                        <div class="flex flex-wrap gap-4 mt-4 text-sm text-gray-500">
                            <span v-if="company.phone">{{ company.phone }}</span>
                            <span v-if="company.email">{{ company.email }}</span>
                            <a v-if="company.website" :href="company.website" target="_blank" class="text-blue-600 hover:underline">{{ company.website }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="vehicles.length" class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Автомобили</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <VehicleCard v-for="v in vehicles" :key="v.id" :vehicle="v" />
                </div>
            </div>

            <div v-if="products.length">
                <h2 class="text-xl font-semibold mb-4">Запчасти</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <ProductCard v-for="p in products" :key="p.id" :product="p" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
