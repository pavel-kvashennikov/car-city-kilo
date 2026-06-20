<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { MapPin, Building2 } from 'lucide-vue-next';

const props = defineProps({ zones: Array });
const selectedZone = ref(null);

const zoneColors = [
    'bg-blue-500', 'bg-teal-500', 'bg-violet-500', 'bg-amber-500',
    'bg-rose-500', 'bg-indigo-500', 'bg-emerald-500', 'bg-orange-500',
];

const occupancyRate = (zone) => {
    if (!zone.locations?.length) return 0;
    return Math.round(zone.locations.filter(l => l.status === 'occupied').length / zone.locations.length * 100);
};

const sortedZones = computed(() => props.zones ?? []);
</script>

<template>
    <AppLayout>
        <div class="container-app py-7">
            <div class="mb-7">
                <h1 class="page-title">Карта авторынка</h1>
                <p class="page-subtitle">Схема расположения павильонов и компаний-резидентов</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- SVG Map placeholder -->
                <div class="lg:col-span-2 card overflow-hidden" style="min-height:480px">
                    <!-- Mini visual map -->
                    <div class="p-4 bg-surface-muted border-b border-outline text-xs text-on-surface-muted flex items-center gap-2">
                        <MapPin class="w-3.5 h-3.5 text-primary" />
                        Интерактивная схема — кликните на зону для просмотра
                    </div>
                    <div class="p-6 flex flex-col items-center justify-center h-full min-h-[400px]">
                        <!-- Stylized grid map -->
                        <div class="grid grid-cols-3 gap-3 w-full max-w-lg">
                            <div
                                v-for="(zone, i) in sortedZones"
                                :key="zone.id"
                                class="relative rounded-xl border-2 p-4 cursor-pointer transition-all"
                                :class="[
                                    selectedZone?.id === zone.id
                                        ? 'border-primary-bright bg-primary-light shadow-md'
                                        : 'border-outline hover:border-primary/40 hover:bg-surface-muted bg-white',
                                ]"
                                @click="selectedZone = zone"
                            >
                                <div :class="['w-8 h-8 rounded-lg flex items-center justify-center mb-2 text-white text-xs font-bold', zoneColors[i % zoneColors.length]]">
                                    {{ zone.code || (i + 1) }}
                                </div>
                                <p class="font-semibold text-xs text-on-surface truncate">{{ zone.name }}</p>
                                <div class="mt-2">
                                    <div class="h-1 rounded-full bg-outline overflow-hidden">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-primary to-primary-bright transition-all"
                                            :style="{ width: occupancyRate(zone) + '%' }"
                                        />
                                    </div>
                                    <p class="text-xs text-on-surface-muted mt-1">{{ occupancyRate(zone) }}% занято</p>
                                </div>
                            </div>

                            <!-- Empty placeholder when no zones -->
                            <div v-if="!sortedZones.length" class="col-span-3 text-center py-12">
                                <MapPin class="w-12 h-12 text-outline mx-auto mb-3" />
                                <p class="text-sm text-on-surface-muted">Данные карты загружаются</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Zone list -->
                    <div class="card p-4">
                        <h3 class="font-bold text-sm text-on-surface mb-3">Зоны рынка</h3>
                        <div class="space-y-2">
                            <button
                                v-for="(zone, i) in sortedZones"
                                :key="zone.id"
                                class="w-full flex items-center gap-3 p-3 rounded-xl border transition-all text-left"
                                :class="selectedZone?.id === zone.id
                                    ? 'border-primary-bright bg-primary-light'
                                    : 'border-outline hover:border-primary/40 hover:bg-surface-muted'"
                                @click="selectedZone = selectedZone?.id === zone.id ? null : zone"
                            >
                                <div :class="['w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0', zoneColors[i % zoneColors.length]]">
                                    {{ zone.code || (i + 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-xs text-on-surface truncate">{{ zone.name }}</p>
                                    <p class="text-xs text-on-surface-muted">
                                        <span class="text-success font-medium">{{ zone.locations?.filter(l => l.status === 'available').length || 0 }}</span> свободно ·
                                        <span class="text-primary font-medium">{{ zone.locations?.filter(l => l.status === 'occupied').length || 0 }}</span> занято
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Selected zone details -->
                    <div v-if="selectedZone" class="card p-4">
                        <h4 class="font-bold text-sm text-on-surface mb-3 flex items-center gap-2">
                            <Building2 class="w-4 h-4 text-primary" />
                            {{ selectedZone.name }} — павильоны
                        </h4>
                        <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1">
                            <div
                                v-for="loc in selectedZone.locations"
                                :key="loc.id"
                                class="flex justify-between items-center py-2.5 px-3 rounded-xl"
                                :class="loc.status === 'occupied' ? 'bg-primary-light' : 'bg-surface-muted'"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-on-surface">{{ loc.code }}</span>
                                    <span v-if="loc.type" class="badge badge-neutral text-xs">{{ loc.type }}</span>
                                </div>
                                <span v-if="loc.company" class="text-xs font-semibold text-primary truncate max-w-[120px]">
                                    {{ loc.company.name }}
                                </span>
                                <span v-else class="text-xs text-success font-medium">Свободен</span>
                            </div>
                            <p v-if="!selectedZone.locations?.length" class="text-xs text-on-surface-muted text-center py-4">
                                Нет данных о павильонах
                            </p>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="card p-4">
                        <h4 class="font-bold text-xs text-on-surface-muted uppercase tracking-wide mb-3">Легенда</h4>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-primary"></div>
                                <span class="text-on-surface-muted">Занято / Резидент</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-success"></div>
                                <span class="text-on-surface-muted">Свободный павильон</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
