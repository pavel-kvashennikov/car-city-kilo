<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import VehicleCard from '@/Components/Catalog/VehicleCard.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ChevronLeft, Phone, CalendarCheck, CreditCard, ArrowLeftRight,
    Gauge, Fuel, Settings, Car, Palette, ShieldCheck, Star, MapPin
} from 'lucide-vue-next';
import { mediaUrl } from '@/lib/mediaUrl';

const props = defineProps({
    vehicle: Object,
    similar: Array,
});

const activePhoto = ref(0);

const fmt = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
const km = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' км';

const photoSrc = (photo) => mediaUrl(photo?.url ?? photo?.path);

const transmissionLabel = {
    automatic: 'Автомат', manual: 'Механика', robot: 'Робот', cvt: 'Вариатор',
};
const driveLabel = {
    fwd: 'Передний', rwd: 'Задний', awd: 'Полный', front: 'Передний', rear: 'Задний', all: 'Полный',
};
const conditionLabel = {
    new: 'Новый', used: 'С пробегом', damaged: 'Битый/восстановленный',
};
const engineLabel = {
    petrol: 'Бензин', diesel: 'Дизель', hybrid: 'Гибрид', electric: 'Электро', gas: 'Газ',
};

// Lead form
const leadForm = useForm({ client_name: '', client_phone: '', message: '', lead_type: 'test_drive', vehicle_id: props.vehicle?.id, dealer_profile_id: props.vehicle?.dealer_profile_id });
const leadSent = ref(false);
function submitLead(type) {
    leadForm.lead_type = type;
    leadForm.post('/leads', { onSuccess: () => { leadSent.value = true; } });
}
</script>

<template>
    <AppLayout>
        <div class="container-app py-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-on-surface-muted mb-5">
                <Link href="/catalog/cars" class="hover:text-primary flex items-center gap-1">
                    <ChevronLeft class="w-3.5 h-3.5" /> Каталог авто
                </Link>
                <span>/</span>
                <span class="text-on-surface">{{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Gallery + Info -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Gallery -->
                    <div class="card overflow-hidden">
                        <div class="aspect-[16/10] bg-surface-muted relative overflow-hidden">
                            <img
                                v-if="vehicle.photos?.length"
                                :src="photoSrc(vehicle.photos[activePhoto])"
                                :alt="`${vehicle.brand?.name} ${vehicle.car_model?.name}`"
                                class="w-full h-full object-cover transition-all duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-surface-dim">
                                <Car class="w-16 h-16 text-outline" />
                            </div>
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex gap-2">
                                <span v-if="vehicle.condition === 'new'" class="badge badge-success">Новый</span>
                                <span v-else class="badge badge-neutral">{{ conditionLabel[vehicle.condition] }}</span>
                            </div>
                        </div>
                        <!-- Thumbnails -->
                        <div v-if="vehicle.photos?.length > 1" class="flex gap-2 p-3 overflow-x-auto bg-surface-muted">
                            <button
                                v-for="(photo, i) in vehicle.photos"
                                :key="photo.id"
                                @click="activePhoto = i"
                                class="shrink-0 w-18 h-14 rounded-lg overflow-hidden border-2 transition-colors"
                                :class="activePhoto === i ? 'border-primary-bright' : 'border-transparent hover:border-outline'"
                                style="width:72px;height:56px"
                            >
                                <img :src="photoSrc(photo)" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Specs grid -->
                    <div class="card p-5">
                        <h2 class="font-bold text-base text-on-surface mb-4">Характеристики</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div v-for="(spec, i) in [
                                { icon: CalendarCheck, label: 'Год', value: vehicle.year },
                                { icon: Gauge, label: 'Пробег', value: vehicle.mileage > 0 ? km(vehicle.mileage) : 'Новый' },
                                { icon: Fuel, label: 'Двигатель', value: `${engineLabel[vehicle.engine_type] || vehicle.engine_type}, ${vehicle.engine_volume} л, ${vehicle.engine_power} л.с.` },
                                { icon: Settings, label: 'КПП', value: transmissionLabel[vehicle.transmission] || vehicle.transmission },
                                { icon: Car, label: 'Привод', value: driveLabel[vehicle.drive_type] || vehicle.drive_type },
                                { icon: Palette, label: 'Цвет', value: vehicle.color },
                                { icon: Car, label: 'Кузов', value: vehicle.body_type },
                                { icon: ShieldCheck, label: 'Статус', value: vehicle.legal_status === 'clean' ? 'Юридически чист' : vehicle.legal_status },
                            ]" :key="i" class="p-3 rounded-xl bg-surface-muted">
                                <div class="flex items-center gap-1.5 text-xs text-on-surface-muted mb-1">
                                    <component :is="spec.icon" class="w-3.5 h-3.5 text-primary" />
                                    {{ spec.label }}
                                </div>
                                <div class="text-sm font-semibold text-on-surface">{{ spec.value }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div v-if="vehicle.features?.length" class="card p-5">
                        <h2 class="font-bold text-base text-on-surface mb-3">Комплектация</h2>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="feat in vehicle.features" :key="feat" class="badge badge-neutral">
                                <Star class="w-2.5 h-2.5" /> {{ feat }}
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="vehicle.description" class="card p-5">
                        <h2 class="font-bold text-base text-on-surface mb-3">Описание</h2>
                        <p class="text-sm text-on-surface-muted leading-relaxed whitespace-pre-line">{{ vehicle.description }}</p>
                    </div>
                </div>

                <!-- Right: Price + actions -->
                <div class="space-y-4">
                    <div class="card p-5 sticky top-20">
                        <h1 class="font-extrabold text-lg text-on-surface">
                            {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
                        </h1>
                        <p class="text-xs text-on-surface-muted mt-1">
                            {{ vehicle.year }} · {{ vehicle.mileage > 0 ? km(vehicle.mileage) : 'Новый' }}
                        </p>

                        <div class="text-3xl font-extrabold text-gradient mt-3">{{ fmt(vehicle.price) }}</div>

                        <div v-if="vehicle.credit_monthly" class="text-xs text-on-surface-muted mt-1">
                            от {{ fmt(vehicle.credit_monthly) }}/мес. в кредит
                        </div>

                        <!-- Dealer info -->
                        <div v-if="vehicle.dealer_profile?.company" class="mt-4 pt-4 border-t border-outline flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ vehicle.dealer_profile.company.name.charAt(0) }}</span>
                            </div>
                            <div>
                                <p class="text-xs text-on-surface-muted">Продавец</p>
                                <p class="text-sm font-semibold text-on-surface">{{ vehicle.dealer_profile.company.name }}</p>
                            </div>
                        </div>

                        <!-- Location badge -->
                        <div class="flex items-center gap-1.5 text-xs text-on-surface-muted mt-3">
                            <MapPin class="w-3.5 h-3.5 text-primary" />
                            Авторынок, Павильон
                        </div>

                        <!-- Actions -->
                        <div v-if="!leadSent" class="mt-5 space-y-2.5">
                            <button class="w-full btn-primary !py-2.5 !text-sm !justify-center">
                                <Phone class="w-4 h-4" /> Позвонить
                            </button>
                            <button @click="submitLead('test_drive')" class="w-full btn-secondary !py-2.5 !text-sm !justify-center">
                                <CalendarCheck class="w-4 h-4" /> Тест-драйв
                            </button>
                            <button @click="submitLead('credit')" class="w-full btn-secondary !py-2.5 !text-sm !justify-center">
                                <CreditCard class="w-4 h-4" /> Рассчитать кредит
                            </button>
                            <button @click="submitLead('trade_in')" class="w-full btn-secondary !py-2.5 !text-sm !justify-center">
                                <ArrowLeftRight class="w-4 h-4" /> Trade-in
                            </button>
                        </div>
                        <div v-else class="mt-5 p-4 rounded-xl bg-success-bg text-center text-sm text-success font-semibold">
                            ✓ Заявка отправлена! Менеджер свяжется с вами.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar vehicles -->
            <div v-if="similar?.length" class="mt-10">
                <h2 class="page-title mb-5">Похожие автомобили</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <VehicleCard v-for="v in similar" :key="v.id" :vehicle="v" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
