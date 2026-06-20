<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import MarketMap from '@/Components/Map/MarketMap.vue';
import { ref, computed } from 'vue';
import { MapPin, Building2, Store, Car } from 'lucide-vue-next';

const props = defineProps({ zones: Array });

const selectedZone = ref(null);
const selectedLocation = ref(null);
const selectedStall = ref(null);

const palette = ['#2563eb', '#0d9488', '#d97706', '#7c3aed', '#db2777', '#4f46e5', '#059669', '#ea580c'];
const zoneColor = (zone, i) => zone?.svg_path?.color || palette[i % palette.length];

const sortedZones = computed(() => props.zones ?? []);

const totals = computed(() => {
    let occupied = 0, available = 0, total = 0;
    for (const z of sortedZones.value) {
        for (const l of z.locations ?? []) {
            total++;
            if (l.status === 'occupied') occupied++;
            else available++;
        }
    }
    return { occupied, available, total };
});

const occupancyRate = (zone) => {
    if (!zone.locations?.length) return 0;
    return Math.round(zone.locations.filter((l) => l.status === 'occupied').length / zone.locations.length * 100);
};

const onSelect = (payload) => {
    if (payload.kind === 'stall') {
        selectedStall.value = payload.stall;
        selectedLocation.value = null;
        return;
    }
    const { location, zone } = payload;
    selectedStall.value = null;
    selectedLocation.value = location;
    selectedZone.value = zone ?? sortedZones.value.find((z) => z.id === location.zone?.id) ?? null;
};

const selectLocation = (location, zone) => onSelect({ kind: 'pavilion', location, zone });

const pickZone = (zone) => {
    selectedZone.value = selectedZone.value?.id === zone.id ? null : zone;
    selectedLocation.value = null;
    selectedStall.value = null;
};

const selectedCode = computed(() => selectedStall.value?.code ?? selectedLocation.value?.code ?? null);

const statusLabel = { occupied: 'Занят', available: 'Свободен', reserved: 'Бронь', maintenance: 'Ремонт' };
</script>

<template>
    <AppLayout>
        <div class="container-app py-7">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="page-title">Карта авторынка</h1>
                    <p class="page-subtitle">Схема расположения павильонов и компаний-резидентов</p>
                </div>
                <div class="flex gap-2">
                    <div class="rounded-xl border border-outline bg-white px-4 py-2 text-center">
                        <p class="text-lg font-bold text-on-surface">{{ totals.total }}</p>
                        <p class="text-xs text-on-surface-muted">павильонов</p>
                    </div>
                    <div class="rounded-xl border border-outline bg-white px-4 py-2 text-center">
                        <p class="text-lg font-bold text-primary">{{ totals.occupied }}</p>
                        <p class="text-xs text-on-surface-muted">занято</p>
                    </div>
                    <div class="rounded-xl border border-outline bg-white px-4 py-2 text-center">
                        <p class="text-lg font-bold text-success">{{ totals.available }}</p>
                        <p class="text-xs text-on-surface-muted">свободно</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Карта -->
                <div class="lg:col-span-2">
                    <div class="mb-2 flex items-center gap-2 text-xs text-on-surface-muted">
                        <MapPin class="h-3.5 h-3.5 w-3.5 text-primary" />
                        Кликните по павильону или парковочному месту · колесо мыши — масштаб · перетаскивание — сдвиг
                    </div>
                    <MarketMap
                        :zones="sortedZones"
                        :selected-code="selectedCode"
                        @select="onSelect"
                    />
                </div>

                <!-- Сайдбар -->
                <div class="space-y-4">
                    <!-- Детали выбранного парковочного места -->
                    <div v-if="selectedStall" class="card p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-2 font-mono text-sm font-bold text-on-surface">
                                <Car class="h-4 w-4 text-primary" /> {{ selectedStall.code }}
                            </span>
                            <span class="badge" :class="selectedStall.occupied ? 'badge-primary' : 'badge-success'">
                                {{ selectedStall.occupied ? 'Занято' : 'Свободно' }}
                            </span>
                        </div>
                        <p class="text-xs text-on-surface-muted">
                            Парковочное место. В дальнейшем сюда можно будет привязать объявление о продаже автомобиля.
                        </p>
                    </div>

                    <!-- Детали выбранного павильона -->
                    <div v-if="selectedLocation" class="card p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="font-mono text-sm font-bold text-on-surface">{{ selectedLocation.code }}</span>
                            <span
                                class="badge"
                                :class="{
                                    'badge-success': selectedLocation.status === 'available',
                                    'badge-primary': selectedLocation.status === 'occupied',
                                    'badge-warning': selectedLocation.status === 'reserved',
                                }"
                            >{{ statusLabel[selectedLocation.status] || selectedLocation.status }}</span>
                        </div>
                        <div v-if="selectedLocation.company" class="rounded-xl bg-primary-light p-3">
                            <div class="flex items-center gap-2">
                                <Store class="h-4 w-4 text-primary" />
                                <p class="font-semibold text-sm text-primary">{{ selectedLocation.company.name }}</p>
                            </div>
                            <p v-if="selectedLocation.type" class="mt-1 text-xs text-on-surface-muted">
                                Тип места: {{ selectedLocation.type }}
                            </p>
                        </div>
                        <p v-else class="text-sm text-success">Павильон свободен — доступен для аренды</p>
                    </div>

                    <!-- Список зон -->
                    <div class="card p-4">
                        <h3 class="mb-3 font-bold text-sm text-on-surface">Зоны рынка</h3>
                        <div class="space-y-2">
                            <button
                                v-for="(zone, i) in sortedZones"
                                :key="zone.id"
                                class="flex w-full items-center gap-3 rounded-xl border p-3 text-left transition-all"
                                :class="selectedZone?.id === zone.id
                                    ? 'border-primary-bright bg-primary-light'
                                    : 'border-outline hover:border-primary/40 hover:bg-surface-muted'"
                                @click="pickZone(zone)"
                            >
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold text-white"
                                    :style="{ backgroundColor: zoneColor(zone, i) }"
                                >{{ zone.code || i + 1 }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-xs text-on-surface">{{ zone.name }}</p>
                                    <p class="text-xs text-on-surface-muted">
                                        <span class="font-medium text-success">{{ zone.locations?.filter((l) => l.status === 'available').length || 0 }}</span> свободно ·
                                        <span class="font-medium text-primary">{{ zone.locations?.filter((l) => l.status === 'occupied').length || 0 }}</span> занято
                                    </p>
                                </div>
                                <span class="text-xs font-semibold text-on-surface-muted">{{ occupancyRate(zone) }}%</span>
                            </button>
                        </div>
                    </div>

                    <!-- Павильоны выбранной зоны -->
                    <div v-if="selectedZone" class="card p-4">
                        <h4 class="mb-3 flex items-center gap-2 font-bold text-sm text-on-surface">
                            <Building2 class="h-4 w-4 text-primary" />
                            {{ selectedZone.name }}
                        </h4>
                        <div class="max-h-72 space-y-1.5 overflow-y-auto pr-1">
                            <button
                                v-for="loc in selectedZone.locations"
                                :key="loc.id"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left"
                                :class="[
                                    loc.status === 'occupied' ? 'bg-primary-light' : 'bg-surface-muted',
                                    selectedLocation?.code === loc.code ? 'ring-2 ring-primary-bright' : '',
                                ]"
                                @click="selectLocation(loc, selectedZone)"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-on-surface">{{ loc.code }}</span>
                                    <span v-if="loc.type" class="badge badge-neutral text-xs">{{ loc.type }}</span>
                                </span>
                                <span v-if="loc.company" class="max-w-[120px] truncate text-xs font-semibold text-primary">
                                    {{ loc.company.name }}
                                </span>
                                <span v-else class="text-xs font-medium text-success">Свободен</span>
                            </button>
                            <p v-if="!selectedZone.locations?.length" class="py-4 text-center text-xs text-on-surface-muted">
                                Нет данных о павильонах
                            </p>
                        </div>
                    </div>

                    <!-- Легенда -->
                    <div class="card p-4">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wide text-on-surface-muted">Легенда</h4>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-3 w-3 rounded-full" style="background:#22c55e" />
                                <span class="text-on-surface-muted">Занято / резидент</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-3 w-3 rounded-full" style="background:#94a3b8" />
                                <span class="text-on-surface-muted">Свободный павильон</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-3 w-3 rounded-full" style="background:#f59e0b" />
                                <span class="text-on-surface-muted">Забронировано</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
