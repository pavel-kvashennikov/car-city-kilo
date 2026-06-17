<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({ zones: Array });
const selectedZone = ref(null);
const selectedLocation = ref(null);
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold mb-8">Карта рынка</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Map SVG Area -->
                <div class="lg:col-span-2 bg-white rounded-xl border p-6 min-h-[500px] flex items-center justify-center">
                    <div class="text-center text-gray-400">
                        <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <p class="text-lg">Интерактивная SVG-карта рынка</p>
                        <p class="text-sm mt-2">Выберите зону для просмотра павильонов</p>
                    </div>
                </div>

                <!-- Zones sidebar -->
                <div class="space-y-4">
                    <div
                        v-for="zone in zones"
                        :key="zone.id"
                        class="bg-white rounded-xl border p-4 cursor-pointer hover:border-blue-500 transition-colors"
                        :class="{ 'border-blue-500 ring-2 ring-blue-100': selectedZone?.id === zone.id }"
                        @click="selectedZone = zone"
                    >
                        <h3 class="font-semibold">{{ zone.name }}</h3>
                        <p class="text-sm text-gray-500">{{ zone.description }}</p>
                        <div class="flex gap-2 mt-2 text-xs">
                            <span class="text-green-600">{{ zone.locations?.filter(l => l.status === 'available').length || 0 }} свободно</span>
                            <span class="text-blue-600">{{ zone.locations?.filter(l => l.status === 'occupied').length || 0 }} занято</span>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div v-if="selectedZone" class="bg-white rounded-xl border p-4">
                        <h4 class="font-semibold mb-3">{{ selectedZone.name }} — павильоны</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            <div
                                v-for="loc in selectedZone.locations"
                                :key="loc.id"
                                class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded"
                            >
                                <span class="font-mono text-sm">{{ loc.code }}</span>
                                <span v-if="loc.company" class="text-sm text-blue-600">{{ loc.company.name }}</span>
                                <span v-else class="text-xs text-green-600">Свободен</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
