<script setup>
import { Link } from '@inertiajs/vue3'
import { Building2, Calendar, Clock, Car, Wrench, User, DollarSign } from 'lucide-vue-next'

defineProps({
    appointment: { type: Object, required: true },
})

const statusMap = {
    pending:     { label: 'Ожидает',       variant: 'warning' },
    confirmed:   { label: 'Подтверждена',  variant: 'success' },
    in_progress: { label: 'В работе',      variant: 'info' },
    completed:   { label: 'Завершена',     variant: 'neutral' },
    cancelled:   { label: 'Отменена',      variant: 'danger' },
}

const statusCls = {
    warning: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
    success: 'bg-green-50 text-green-700 border border-green-200',
    info:    'bg-blue-50 text-blue-700 border border-blue-200',
    neutral: 'bg-surface-muted text-on-surface-muted border border-outline',
    danger:  'bg-red-50 text-red-700 border border-red-200',
}

const getStatus = (s) => statusMap[s] ?? statusMap.pending

const fmtDate = (d) => d
    ? new Date(d).toLocaleDateString('ru-RU', { weekday: 'long', day: 'numeric', month: 'long' })
    : null

const fmtTime = (t) => t ? t.slice(0, 5) : null

const fmt = (v) => v ? new Intl.NumberFormat('ru-RU').format(v) + ' ₽' : null
</script>

<template>
    <div class="card overflow-hidden">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4 p-5 pb-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shrink-0 shadow-sm">
                    <span class="text-white font-bold text-base">
                        {{ appointment.service_profile?.company?.name?.charAt(0) ?? 'С' }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-on-surface truncate">
                        {{ appointment.service_profile?.company?.name ?? 'Сервис' }}
                    </p>
                    <Link
                        v-if="appointment.service_profile?.slug"
                        :href="`/catalog/services/${appointment.service_profile.slug}`"
                        class="text-xs text-primary hover:underline"
                    >
                        Страница сервиса
                    </Link>
                </div>
            </div>

            <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                :class="statusCls[getStatus(appointment.status).variant]">
                {{ getStatus(appointment.status).label }}
            </span>
        </div>

        <!-- Details grid -->
        <div class="px-5 pb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">

            <!-- Date & Time -->
            <div v-if="appointment.time_slot" class="flex items-start gap-2.5">
                <Calendar class="w-4 h-4 text-primary mt-0.5 shrink-0" />
                <div>
                    <p class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide mb-0.5">Дата и время</p>
                    <p class="text-sm text-on-surface font-medium">
                        {{ fmtDate(appointment.time_slot.date) }}
                    </p>
                    <p class="text-sm text-on-surface-muted">
                        {{ fmtTime(appointment.time_slot.start_time) }}
                        <template v-if="appointment.time_slot.end_time">
                            — {{ fmtTime(appointment.time_slot.end_time) }}
                        </template>
                    </p>
                </div>
            </div>

            <!-- Service item -->
            <div v-if="appointment.service_item" class="flex items-start gap-2.5">
                <Wrench class="w-4 h-4 text-primary mt-0.5 shrink-0" />
                <div>
                    <p class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide mb-0.5">Услуга</p>
                    <p class="text-sm text-on-surface font-medium">{{ appointment.service_item.name }}</p>
                    <p v-if="appointment.service_item.duration_minutes" class="text-xs text-on-surface-muted">
                        ~{{ appointment.service_item.duration_minutes }} мин.
                    </p>
                </div>
            </div>

            <!-- Vehicle -->
            <div v-if="appointment.vehicle_brand || appointment.vehicle_model" class="flex items-start gap-2.5">
                <Car class="w-4 h-4 text-primary mt-0.5 shrink-0" />
                <div>
                    <p class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide mb-0.5">Автомобиль</p>
                    <p class="text-sm text-on-surface font-medium">
                        {{ [appointment.vehicle_brand, appointment.vehicle_model, appointment.vehicle_year].filter(Boolean).join(' ') }}
                    </p>
                </div>
            </div>

            <!-- Master -->
            <div v-if="appointment.master" class="flex items-start gap-2.5">
                <User class="w-4 h-4 text-primary mt-0.5 shrink-0" />
                <div>
                    <p class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide mb-0.5">Мастер</p>
                    <p class="text-sm text-on-surface font-medium">{{ appointment.master.name }}</p>
                    <p v-if="appointment.master.specialization" class="text-xs text-on-surface-muted">
                        {{ appointment.master.specialization }}
                    </p>
                </div>
            </div>

            <!-- Estimated cost -->
            <div v-if="appointment.estimated_cost" class="flex items-start gap-2.5">
                <DollarSign class="w-4 h-4 text-primary mt-0.5 shrink-0" />
                <div>
                    <p class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide mb-0.5">Стоимость</p>
                    <p class="text-sm font-bold text-gradient">{{ fmt(appointment.estimated_cost) }}</p>
                </div>
            </div>
        </div>

        <!-- Comment -->
        <div v-if="appointment.comment" class="mx-5 mb-4 p-3 rounded-lg bg-surface-muted/60 text-sm text-on-surface-muted italic">
            "{{ appointment.comment }}"
        </div>

        <!-- Footer -->
        <div class="border-t border-outline px-5 py-3 flex items-center justify-between bg-surface-muted/30">
            <p class="text-xs text-on-surface-muted">
                Записано: {{ new Date(appointment.created_at).toLocaleDateString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric' }) }}
            </p>
            <p class="text-xs text-on-surface-muted">{{ appointment.client_phone }}</p>
        </div>
    </div>
</template>
