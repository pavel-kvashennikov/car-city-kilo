<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronLeft, Clock, Wrench, Users, CalendarCheck } from 'lucide-vue-next';

const props = defineProps({ service: Object });

const fmt = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' ₽';

// Simple appointment form
const apptForm = useForm({
    service_profile_id: props.service?.id,
    service_item_id: null,
    client_name: '',
    client_phone: '',
    vehicle_brand: '',
    vehicle_model: '',
    vehicle_year: '',
    comment: '',
    status: 'pending',
});
const apptSent = ref(false);
function bookAppointment() {
    apptForm.post('/appointments', {
        onSuccess: () => { apptSent.value = true; },
    });
}
</script>

<template>
    <AppLayout>
        <div class="container-app py-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-on-surface-muted mb-5">
                <Link href="/catalog/services" class="hover:text-primary flex items-center gap-1">
                    <ChevronLeft class="w-3.5 h-3.5" /> Сервисы
                </Link>
                <span>/</span>
                <span class="text-on-surface">{{ service.company?.name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: info -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Header -->
                    <div class="card p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div v-if="service.company?.logo_path" class="w-14 h-14 rounded-xl overflow-hidden ring-2 ring-outline">
                                <img :src="service.company.logo_path" :alt="service.company?.name" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shadow-sm">
                                <span class="text-white font-bold text-xl">{{ service.company?.name?.charAt(0) }}</span>
                            </div>
                            <div>
                                <h1 class="font-extrabold text-lg text-on-surface">{{ service.company?.name }}</h1>
                                <p v-if="service.company?.phone" class="text-sm text-on-surface-muted mt-0.5">{{ service.company.phone }}</p>
                            </div>
                        </div>
                        <p v-if="service.description" class="text-sm text-on-surface-muted leading-relaxed mb-4">{{ service.description }}</p>
                        <div v-if="service.specializations?.length" class="flex flex-wrap gap-2">
                            <span v-for="spec in service.specializations" :key="spec" class="badge badge-primary">{{ spec }}</span>
                        </div>
                    </div>

                    <!-- Services list -->
                    <div v-if="service.service_items?.length" class="card p-5">
                        <h2 class="font-bold text-sm text-on-surface mb-4 flex items-center gap-2">
                            <Wrench class="w-4 h-4 text-primary" /> Прайс-лист услуг
                        </h2>
                        <div class="space-y-2">
                            <div
                                v-for="item in service.service_items"
                                :key="item.id"
                                class="flex items-center justify-between py-3 px-4 rounded-xl hover:bg-surface-muted transition-colors cursor-pointer"
                                @click="apptForm.service_item_id = item.id"
                                :class="apptForm.service_item_id === item.id ? 'bg-primary-light ring-1 ring-primary-bright' : ''"
                            >
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-sm text-on-surface">{{ item.name }}</p>
                                    <p v-if="item.duration_minutes" class="flex items-center gap-1 text-xs text-on-surface-muted mt-0.5">
                                        <Clock class="w-3 h-3" /> ~{{ item.duration_minutes }} мин.
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <p v-if="item.price_fixed" class="font-bold text-sm text-gradient">{{ fmt(item.price_fixed) }}</p>
                                    <p v-else-if="item.price_from" class="font-bold text-sm text-gradient">
                                        от {{ fmt(item.price_from) }}
                                        <span v-if="item.price_to"> – {{ fmt(item.price_to) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Masters -->
                    <div v-if="service.masters?.length" class="card p-5">
                        <h2 class="font-bold text-sm text-on-surface mb-4 flex items-center gap-2">
                            <Users class="w-4 h-4 text-primary" /> Мастера
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div v-for="master in service.masters" :key="master.id" class="text-center p-3 rounded-xl bg-surface-muted">
                                <div class="w-14 h-14 rounded-full overflow-hidden mx-auto mb-2 ring-2 ring-outline">
                                    <img v-if="master.photo_path" :src="master.photo_path" :alt="master.name" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                                        <span class="text-white font-bold">{{ master.name?.charAt(0) }}</span>
                                    </div>
                                </div>
                                <p class="font-semibold text-xs text-on-surface">{{ master.name }}</p>
                                <p class="text-xs text-on-surface-muted mt-0.5">{{ master.specialization }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: booking -->
                <div>
                    <div class="card p-5 sticky top-20">
                        <h2 class="font-bold text-base text-on-surface mb-4 flex items-center gap-2">
                            <CalendarCheck class="w-5 h-5 text-primary" /> Онлайн-запись
                        </h2>

                        <div v-if="apptSent" class="p-4 rounded-xl bg-success-bg text-center text-sm text-success font-semibold">
                            ✓ Заявка отправлена! Сервис свяжется с вами для подтверждения.
                        </div>

                        <form v-else @submit.prevent="bookAppointment" class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-on-surface-muted mb-1 uppercase tracking-wide">Ваше имя *</label>
                                <input v-model="apptForm.client_name" required class="input-field text-sm" placeholder="Иван Иванов" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-on-surface-muted mb-1 uppercase tracking-wide">Телефон *</label>
                                <input v-model="apptForm.client_phone" required class="input-field text-sm" placeholder="+7 (999) 123-45-67" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-on-surface-muted mb-1 uppercase tracking-wide">Марка авто</label>
                                    <input v-model="apptForm.vehicle_brand" class="input-field text-sm" placeholder="Toyota" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-on-surface-muted mb-1 uppercase tracking-wide">Модель</label>
                                    <input v-model="apptForm.vehicle_model" class="input-field text-sm" placeholder="Camry" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-on-surface-muted mb-1 uppercase tracking-wide">Год</label>
                                <input v-model="apptForm.vehicle_year" type="number" class="input-field text-sm" placeholder="2020" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-on-surface-muted mb-1 uppercase tracking-wide">Комментарий</label>
                                <textarea v-model="apptForm.comment" class="input-field text-sm resize-none" rows="2" placeholder="Опишите проблему..."></textarea>
                            </div>

                            <div v-if="apptForm.service_item_id" class="text-xs text-primary font-medium">
                                ✓ Услуга выбрана — нажмите «Записаться»
                            </div>

                            <button type="submit" class="w-full btn-primary !justify-center !text-sm !py-2.5" :disabled="apptForm.processing">
                                <CalendarCheck class="w-4 h-4" />
                                {{ apptForm.processing ? 'Отправка...' : 'Записаться' }}
                            </button>
                            <p class="text-xs text-on-surface-muted text-center">Нажимая, вы соглашаетесь с условиями сервиса</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
