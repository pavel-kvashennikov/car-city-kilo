<script setup>
defineProps({
    appointment: { type: Object, required: true },
})

const statusMap = {
    pending: { label: 'Ожидает', class: 'bg-yellow-100 text-yellow-800' },
    confirmed: { label: 'Подтверждена', class: 'bg-green-100 text-green-800' },
    in_progress: { label: 'В работе', class: 'bg-blue-100 text-blue-800' },
    completed: { label: 'Завершена', class: 'bg-gray-100 text-gray-800' },
    cancelled: { label: 'Отменена', class: 'bg-red-100 text-red-800' },
}
</script>

<template>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-start justify-between">
            <div>
                <h4 class="font-semibold">{{ appointment.service_profile?.company?.name }}</h4>
                <p class="text-sm text-gray-500 mt-1">
                    {{ appointment.time_slot?.date }} · {{ appointment.time_slot?.start_time }} — {{ appointment.time_slot?.end_time }}
                </p>
                <p v-if="appointment.car_brand" class="text-sm text-gray-500">
                    {{ appointment.car_brand }} {{ appointment.car_model }} {{ appointment.car_year }}
                </p>
            </div>
            <span
                class="text-xs px-2 py-1 rounded-full"
                :class="(statusMap[appointment.status] || statusMap.pending).class"
            >
                {{ (statusMap[appointment.status] || statusMap.pending).label }}
            </span>
        </div>
        <div v-if="appointment.service_items" class="mt-3 pt-3 border-t">
            <p class="text-xs text-gray-500 mb-1">Услуги:</p>
            <ul class="text-sm space-y-1">
                <li v-for="(item, i) in appointment.service_items" :key="i">{{ item.name }}</li>
            </ul>
        </div>
    </div>
</template>
