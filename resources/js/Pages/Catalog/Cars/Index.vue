<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold mb-8">Автомобили</h1>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Filters Sidebar -->
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-xl border p-6 space-y-4 sticky top-24">
                        <h3 class="font-semibold text-lg">Фильтры</h3>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Марка</label>
                            <select v-model="form.brand_id" @change="applyFilters" class="w-full border rounded-lg px-3 py-2">
                                <option :value="undefined">Все марки</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Цена от</label>
                                <input v-model="form.price_from" type="number" class="w-full border rounded-lg px-3 py-2" @change="applyFilters" />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">до</label>
                                <input v-model="form.price_to" type="number" class="w-full border rounded-lg px-3 py-2" @change="applyFilters" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Год от</label>
                                <input v-model="form.year_from" type="number" class="w-full border rounded-lg px-3 py-2" @change="applyFilters" />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">до</label>
                                <input v-model="form.year_to" type="number" class="w-full border rounded-lg px-3 py-2" @change="applyFilters" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">КПП</label>
                            <select v-model="form.transmission" @change="applyFilters" class="w-full border rounded-lg px-3 py-2">
                                <option :value="undefined">Любая</option>
                                <option value="automatic">Автомат</option>
                                <option value="manual">Механика</option>
                                <option value="robot">Робот</option>
                                <option value="cvt">Вариатор</option>
                            </select>
                        </div>
                    </div>
                </aside>

                <!-- Vehicle Grid -->
                <div class="lg:col-span-3">
                    <div v-if="vehicles.data.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <Link
                            v-for="vehicle in vehicles.data"
                            :key="vehicle.id"
                            :href="`/catalog/cars/${vehicle.slug}`"
                            class="bg-white rounded-xl border overflow-hidden hover:shadow-md transition-shadow"
                        >
                            <div class="aspect-[4/3] bg-gray-100">
                                <img
                                    v-if="vehicle.photos?.[0]"
                                    :src="`/storage/${vehicle.photos[0].path}`"
                                    :alt="vehicle.brand?.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    Нет фото
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">
                                    {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ vehicle.year }} · {{ vehicle.mileage?.toLocaleString() }} км · {{ vehicle.engine_volume }}л
                                </p>
                                <p class="text-lg font-bold text-blue-600 mt-2">
                                    {{ Number(vehicle.price).toLocaleString() }} ₽
                                </p>
                            </div>
                        </Link>
                    </div>
                    <div v-else class="text-center py-20 text-gray-500">
                        Автомобили не найдены. Попробуйте изменить фильтры.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
