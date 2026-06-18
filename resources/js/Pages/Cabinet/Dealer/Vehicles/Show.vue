<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import Badge from '@/Components/UI/Badge.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    vehicle: { type: Object, required: true },
})

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽'
</script>

<template>
    <CabinetLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">
                {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }} {{ vehicle.year }}
            </h1>
            <Link :href="`/cabinet/dealer/vehicles/${vehicle.id}/edit`" class="text-blue-600 hover:underline">
                Редактировать
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6 space-y-3">
                <p><span class="text-gray-500">Цена:</span> <strong>{{ formatPrice(vehicle.price) }}</strong></p>
                <p><span class="text-gray-500">Пробег:</span> {{ vehicle.mileage?.toLocaleString('ru-RU') }} км</p>
                <p><span class="text-gray-500">VIN:</span> {{ vehicle.vin ?? '—' }}</p>
                <p>
                    <span class="text-gray-500">Статус:</span>
                    <Badge class="ml-2">{{ vehicle.status }}</Badge>
                </p>
                <p v-if="vehicle.description" class="text-gray-700">{{ vehicle.description }}</p>
            </div>

            <div v-if="vehicle.photos?.length" class="grid grid-cols-2 gap-2">
                <img
                    v-for="photo in vehicle.photos"
                    :key="photo.id"
                    :src="photo.url ?? photo.path"
                    :alt="vehicle.brand?.name"
                    class="rounded-lg object-cover h-32 w-full"
                />
            </div>
        </div>

        <div v-if="vehicle.leads?.length" class="mt-8">
            <h2 class="font-semibold mb-4">Заявки ({{ vehicle.leads.length }})</h2>
            <div class="space-y-2">
                <div v-for="lead in vehicle.leads" :key="lead.id" class="bg-white rounded-xl shadow p-4">
                    <p class="font-medium">{{ lead.type }} — {{ lead.client_name }}</p>
                    <p class="text-sm text-gray-500">{{ lead.client_phone }}</p>
                </div>
            </div>
        </div>
    </CabinetLayout>
</template>
