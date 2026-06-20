<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import AppointmentCard from '@/Components/Service/AppointmentCard.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import { Link } from '@inertiajs/vue3'
import { CalendarCheck } from 'lucide-vue-next'

defineProps({
    appointments: { type: Object, default: () => ({ data: [], links: [] }) },
})
</script>

<template>
    <AppLayout>
        <div class="container-app max-w-3xl py-8">
            <h1 class="page-title mb-6">Мои записи на сервис</h1>

            <div v-if="!appointments.data?.length" class="card p-16 text-center">
                <CalendarCheck class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">У вас пока нет записей</p>
                <p class="text-sm text-on-surface-muted mt-1 mb-4">
                    Выберите сервис и запишитесь онлайн — подтверждение придёт по телефону
                </p>
                <Link href="/catalog/services" class="btn-primary text-sm inline-flex">
                    Найти автосервис
                </Link>
            </div>

            <div v-else class="space-y-4">
                <AppointmentCard
                    v-for="appt in appointments.data"
                    :key="appt.id"
                    :appointment="appt"
                />
            </div>

            <div class="mt-6">
                <Pagination :links="appointments.links" />
            </div>
        </div>
    </AppLayout>
</template>
