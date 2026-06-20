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
        <div class="container-app max-w-4xl py-8">
            <h1 class="page-title mb-6">Мои записи на сервис</h1>
            <div v-if="appointments.data.length === 0" class="card p-12 text-center text-on-surface-muted">
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
