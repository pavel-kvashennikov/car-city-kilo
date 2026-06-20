<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import ProductCard from '@/Components/Catalog/ProductCard.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Search, SlidersHorizontal, X } from 'lucide-vue-next';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const form = ref({ ...props.filters });

function applyFilters() {
    const params = Object.fromEntries(Object.entries(form.value).filter(([, v]) => v !== undefined && v !== ''));
    router.get('/catalog/parts', params, { preserveState: true, preserveScroll: true });
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
            <!-- Header + search -->
            <div class="mb-6">
                <h1 class="page-title">Запчасти</h1>
                <p class="page-subtitle">Поиск по названию, OEM-номеру и артикулу. Найдено: {{ products.total ?? products.data?.length ?? 0 }}</p>
            </div>

            <!-- Search bar -->
            <div class="card p-4 mb-6">
                <div class="flex gap-3">
                    <div class="search-pill flex-1 !py-2">
                        <Search class="w-4 h-4 text-secondary shrink-0" />
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Название, OEM-номер или артикул..."
                            class="flex-1 bg-transparent border-none outline-none text-sm"
                            @keyup.enter="applyFilters"
                        />
                        <button v-if="form.search" @click="form.search = ''; applyFilters()" class="text-secondary hover:text-danger">
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <button @click="applyFilters" class="btn-primary !text-sm">Найти</button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar -->
                <aside class="hidden lg:block lg:col-span-1">
                    <div class="card p-5 space-y-4 lg:sticky lg:top-20">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-sm text-on-surface">Фильтры</h3>
                            <button v-if="hasFilters" @click="clearFilters" class="text-xs text-danger hover:underline flex items-center gap-1">
                                <X class="w-3 h-3" /> Сбросить
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Категория</label>
                            <select v-model="form.category_id" class="input-field text-sm" @change="applyFilters">
                                <option :value="undefined">Все категории</option>
                                <template v-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id">{{ cat.name }}</option>
                                    <option v-for="child in cat.children" :key="child.id" :value="child.id">
                                        &nbsp;&nbsp; {{ child.name }}
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Состояние</label>
                            <select v-model="form.condition" class="input-field text-sm" @change="applyFilters">
                                <option :value="undefined">Любое</option>
                                <option value="new">Новая</option>
                                <option value="used">Б/У</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Цена, ₽</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input v-model="form.price_from" type="number" placeholder="От" class="input-field text-sm" @change="applyFilters" />
                                <input v-model="form.price_to" type="number" placeholder="До" class="input-field text-sm" @change="applyFilters" />
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Results -->
                <div class="lg:col-span-3">
                    <div v-if="products.data?.length" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                    </div>

                    <div v-else class="card text-center py-16 px-6">
                        <div class="text-4xl mb-3">🔩</div>
                        <p class="font-semibold text-on-surface mb-1">Запчасти не найдены</p>
                        <p class="text-sm text-on-surface-muted mb-4">Попробуйте другой запрос</p>
                        <button @click="clearFilters" class="btn-primary !text-sm">Очистить</button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
                        <Link
                            v-for="page in products.links"
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
