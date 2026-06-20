<script setup>
import { Link } from '@inertiajs/vue3';
import { Car, Star, Heart } from 'lucide-vue-next';
import { computed } from 'vue';
import { useFavorites } from '@/composables/useFavorites';
import { mediaUrl } from '@/lib/mediaUrl';

const props = defineProps({ vehicle: { type: Object, required: true } });

const fmt = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
const km = (n) => n > 0 ? new Intl.NumberFormat('ru-RU').format(n) + ' км' : 'Новый';
const txLabel = { automatic: 'Автомат', manual: 'Механика', robot: 'Робот', cvt: 'Вариатор' };
const photoSrc = (p) => mediaUrl(p?.url ?? p?.path);

const { isVehicleFavorited, toggle } = useFavorites();
const isFav = computed(() => isVehicleFavorited(props.vehicle.id));

function handleFav(e) {
    e.preventDefault();
    e.stopPropagation();
    toggle('vehicle', props.vehicle.id);
}
</script>

<template>
    <Link :href="`/catalog/cars/${vehicle.slug}`" class="card card-hover overflow-hidden group block">
        <div class="aspect-[16/10] bg-surface-muted relative overflow-hidden">
            <img
                v-if="photoSrc(vehicle.photos?.[0])"
                :src="photoSrc(vehicle.photos[0])"
                :alt="`${vehicle.brand?.name} ${vehicle.car_model?.name}`"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-surface-dim">
                <Car class="w-10 h-10 text-outline" />
            </div>
            <div class="absolute top-2.5 left-2.5 flex gap-1.5">
                <span v-if="vehicle.condition === 'new'" class="badge badge-success">Новый</span>
                <span v-if="vehicle.is_featured" class="badge badge-primary">
                    <Star class="w-2.5 h-2.5" />Топ
                </span>
            </div>
            <div class="absolute top-2.5 right-2.5 flex items-center gap-1.5">
                <button
                    @click="handleFav"
                    class="w-7 h-7 rounded-full flex items-center justify-center bg-white/80 backdrop-blur-sm shadow-sm hover:bg-white transition-colors"
                    :title="isFav ? 'Убрать из избранного' : 'В избранное'"
                >
                    <Heart
                        class="w-3.5 h-3.5 transition-colors"
                        :class="isFav ? 'fill-red-500 text-red-500' : 'text-on-surface-muted'"
                    />
                </button>
                <span class="badge badge-neutral">{{ vehicle.year }}</span>
            </div>
        </div>
        <div class="p-4">
            <h3 class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors truncate">
                {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
            </h3>
            <p class="text-xs text-on-surface-muted mt-1">
                {{ km(vehicle.mileage) }}
                <template v-if="vehicle.transmission">
                    <span class="opacity-40 mx-1">·</span>{{ txLabel[vehicle.transmission] || vehicle.transmission }}
                </template>
                <template v-if="vehicle.engine_volume">
                    <span class="opacity-40 mx-1">·</span>{{ vehicle.engine_volume }} л
                </template>
            </p>
            <p class="text-lg font-extrabold text-gradient mt-2.5">{{ fmt(vehicle.price) }}</p>
        </div>
    </Link>
</template>
