<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, Pencil, Car, Phone } from 'lucide-vue-next';
import { mediaUrl } from '@/lib/mediaUrl';

defineProps({
    vehicle: { type: Object, required: true },
});

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽';
const statusVariant = (s) => ({ active: 'success', draft: 'default', pending: 'warning', sold: 'info', archived: 'default' }[s] || 'default');
const statusLabel = (s) => ({ active: 'Активно', draft: 'Черновик', pending: 'На модерации', sold: 'Продано', archived: 'Архив' }[s] || s);
const photoSrc = (p) => mediaUrl(p?.path) ?? p?.url ?? null;
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/dealer/vehicles" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку
                    </Link>
                    <h1 class="page-title">{{ vehicle.brand?.name }} {{ vehicle.car_model?.name }} {{ vehicle.year }}</h1>
                </div>
                <Link :href="`/cabinet/dealer/vehicles/${vehicle.id}/edit`" class="btn-primary !text-sm shrink-0">
                    <Pencil class="w-4 h-4" /> Редактировать
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="card p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-extrabold text-gradient">{{ fmt(vehicle.price) }}</span>
                        <Badge :variant="statusVariant(vehicle.status)">{{ statusLabel(vehicle.status) }}</Badge>
                    </div>
                    <dl class="divide-y divide-outline text-sm">
                        <div class="flex justify-between py-2"><dt class="text-on-surface-muted">Пробег</dt><dd class="font-medium">{{ vehicle.mileage?.toLocaleString('ru-RU') }} км</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-on-surface-muted">VIN</dt><dd class="font-medium font-mono">{{ vehicle.vin ?? '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-on-surface-muted">Цвет</dt><dd class="font-medium">{{ vehicle.color ?? '—' }}</dd></div>
                    </dl>
                    <p v-if="vehicle.description" class="text-sm text-on-surface-muted leading-relaxed pt-2">{{ vehicle.description }}</p>
                </div>

                <div v-if="vehicle.photos?.length" class="grid grid-cols-2 gap-2 content-start">
                    <div v-for="photo in vehicle.photos" :key="photo.id" class="aspect-[4/3] rounded-xl overflow-hidden bg-surface-muted">
                        <img :src="photoSrc(photo)" :alt="vehicle.brand?.name" class="w-full h-full object-cover" />
                    </div>
                </div>
                <div v-else class="card p-8 flex flex-col items-center justify-center text-on-surface-muted">
                    <Car class="w-10 h-10 mb-2 text-outline" />
                    <p class="text-sm">Фотографии не загружены</p>
                </div>
            </div>

            <div v-if="vehicle.leads?.length" class="card p-5">
                <h2 class="font-bold text-sm text-on-surface mb-4">Заявки ({{ vehicle.leads.length }})</h2>
                <div class="space-y-2">
                    <div v-for="lead in vehicle.leads" :key="lead.id" class="flex items-center justify-between p-3 rounded-xl bg-surface-muted">
                        <div>
                            <p class="font-semibold text-sm text-on-surface">{{ lead.client_name }}</p>
                            <p class="text-xs text-on-surface-muted">{{ lead.lead_type ?? lead.type }}</p>
                        </div>
                        <a :href="`tel:${lead.client_phone}`" class="inline-flex items-center gap-1.5 text-sm text-primary font-medium">
                            <Phone class="w-3.5 h-3.5" /> {{ lead.client_phone }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </CabinetLayout>
</template>
