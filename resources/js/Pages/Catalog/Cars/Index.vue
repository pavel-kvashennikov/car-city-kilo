<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import VehicleCard from '@/Components/Catalog/VehicleCard.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    vehicles: Object,
    brands: Array,
    filters: Object,
});

const form = ref({ ...props.filters });

function applyFilters() {
    router.get('/catalog/cars', form.value, { preserveState: true });
}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-8">
                <h1 class="page-title">Автомобили</h1>
                <p class="page-subtitle">Новые и с пробегом от резидентов авторынка</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <aside class="lg:col-span-1">
                    <div class="surface-panel p-6 space-y-5 sticky top-24">
                        <h3 class="font-semibold text-on-surface text-lg">Фильтры</h3>

                        <div>
                            <label class="block text-sm font-medium text-on-surface-muted mb-1.5">Марка</label>
                            <select v-model="form.brand_id" class="input-field" @change="applyFilters">
                                <option :value="undefined">Все марки</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-on-surface-muted mb-1.5">Цена от</label>
                                <input v-model="form.price_from" type="number" class="input-field" @change="applyFilters" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-muted mb-1.5">до</label>
                                <input v-model="form.price_to" type="number" class="input-field" @change="applyFilters" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-on-surface-muted mb-1.5">Год от</label>
                                <input v-model="form.year_from" type="number" class="input-field" @change="applyFilters" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-muted mb-1.5">до</label>
                                <input v-model="form.year_to" type="number" class="input-field" @change="applyFilters" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-on-surface-muted mb-1.5">КПП</label>
                            <select v-model="form.transmission" class="input-field" @change="applyFilters">
                                <option :value="undefined">Любая</option>
                                <option value="automatic">Автомат</option>
                                <option value="manual">Механика</option>
                                <option value="robot">Робот</option>
                                <option value="cvt">Вариатор</option>
                            </select>
                        </div>
                    </div>
                </aside>

                <div class="lg:col-span-3">
                    <div v-if="vehicles.data.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <VehicleCard v-for="vehicle in vehicles.data" :key="vehicle.id" :vehicle="vehicle" />
                    </div>
                    <div v-else class="surface-panel text-center py-20">
                        <p class="text-on-surface-muted">Автомобили не найдены. Попробуйте изменить фильтры.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
