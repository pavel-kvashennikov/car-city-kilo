<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import VehicleCard from '@/Components/Catalog/VehicleCard.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { SlidersHorizontal, X } from 'lucide-vue-next';

const props = defineProps({
    vehicles: Object,
    brands: Array,
    filters: Object,
});

const form = ref({ ...props.filters });
const mobileFiltersOpen = ref(false);

function applyFilters() {
    const params = Object.fromEntries(Object.entries(form.value).filter(([, v]) => v !== undefined && v !== ''));
    router.get('/catalog/cars', params, { preserveState: true, preserveScroll: true });
}

function clearFilters() {
    form.value = {};
    applyFilters();
}

const hasFilters = Object.values(props.filters || {}).some(v => v !== undefined && v !== '');
</script>

<template>
    <AppLayout>
        <div class="container-app py-7">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6 gap-4">
                <div>
                    <h1 class="page-title">Автомобили</h1>
                    <p class="page-subtitle">
                        Найдено: {{ vehicles.total ?? vehicles.data?.length ?? 0 }} предложений
                    </p>
                </div>
                <button
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    class="btn-secondary !text-sm lg:hidden flex items-center gap-2"
                >
                    <SlidersHorizontal class="w-4 h-4" />
                    Фильтры
                    <span v-if="hasFilters" class="w-2 h-2 rounded-full bg-primary" />
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar filters -->
                <aside
                    class="lg:col-span-1"
                    :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'"
                >
                    <div class="card p-5 space-y-4 lg:sticky lg:top-20">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-sm text-on-surface">Фильтры</h3>
                            <button v-if="hasFilters" @click="clearFilters" class="text-xs text-danger hover:underline flex items-center gap-1">
                                <X class="w-3 h-3" /> Сбросить
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Марка</label>
                            <select v-model="form.brand_id" class="input-field text-sm" @change="applyFilters">
                                <option :value="undefined">Все марки</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Цена, ₽</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input v-model="form.price_from" type="number" placeholder="От" class="input-field text-sm" @change="applyFilters" />
                                <input v-model="form.price_to" type="number" placeholder="До" class="input-field text-sm" @change="applyFilters" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Год выпуска</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input v-model="form.year_from" type="number" placeholder="От" class="input-field text-sm" @change="applyFilters" />
                                <input v-model="form.year_to" type="number" placeholder="До" class="input-field text-sm" @change="applyFilters" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">КПП</label>
                            <select v-model="form.transmission" class="input-field text-sm" @change="applyFilters">
                                <option :value="undefined">Любая</option>
                                <option value="automatic">Автомат</option>
                                <option value="manual">Механика</option>
                                <option value="robot">Робот</option>
                                <option value="cvt">Вариатор</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Состояние</label>
                            <select v-model="form.condition" class="input-field text-sm" @change="applyFilters">
                                <option :value="undefined">Любое</option>
                                <option value="new">Новый</option>
                                <option value="used">С пробегом</option>
                            </select>
                        </div>
                    </div>
                </aside>

                <!-- Results -->
                <div class="lg:col-span-3">
                    <div v-if="vehicles.data?.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        <VehicleCard v-for="vehicle in vehicles.data" :key="vehicle.id" :vehicle="vehicle" />
                    </div>

                    <div v-else class="card text-center py-16 px-6">
                        <div class="text-4xl mb-3">🚗</div>
                        <p class="font-semibold text-on-surface mb-1">Автомобили не найдены</p>
                        <p class="text-sm text-on-surface-muted mb-4">Попробуйте изменить или сбросить фильтры</p>
                        <button @click="clearFilters" class="btn-primary !text-sm">Сбросить фильтры</button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="vehicles.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
                        <Link
                            v-for="page in vehicles.links"
                            :key="page.label"
                            :href="page.url || '#'"
                            v-html="page.label"
                            class="min-w-[36px] h-9 flex items-center justify-center px-3 rounded-lg text-sm border transition-colors"
                            :class="page.active
                                ? 'border-primary bg-primary-light text-primary font-semibold'
                                : page.url
                                    ? 'border-outline text-on-surface-muted hover:border-primary hover:text-primary'
                                    : 'border-transparent text-outline cursor-not-allowed'"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
