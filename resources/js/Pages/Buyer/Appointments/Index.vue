<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import AppointmentCard from '@/Components/Service/AppointmentCard.vue'
import Pagination from '@/Components/UI/Pagination.vue'

defineProps({
    appointments: { type: Object, default: () => ({ data: [], links: [] }) },
})
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Мои записи на сервис</h1>
            <div v-if="appointments.data.length === 0" class="text-center text-gray-500 py-12">
                У вас пока нет записей
            </div>
            <div v-else class="space-y-4">
                <AppointmentCard v-for="appt in appointments.data" :key="appt.id" :appointment="appt" />
            </div>
            <div class="mt-6">
                <Pagination :links="appointments.links" />
            </div>
        </div>
    </AppLayout>
</template>
