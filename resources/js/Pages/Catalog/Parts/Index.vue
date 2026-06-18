<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import ProductCard from '@/Components/Catalog/ProductCard.vue';
import Button from '@/Components/UI/Button.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Search } from 'lucide-vue-next';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');

function doSearch() {
    router.get('/catalog/parts', { search: search.value }, { preserveState: true });
}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-8">
                <h1 class="page-title">Запчасти</h1>
                <p class="page-subtitle">Поиск по названию, OEM-номеру и артикулу</p>
            </div>

            <div class="surface-panel p-6 mb-8">
                <form class="flex flex-col sm:flex-row gap-3" @submit.prevent="doSearch">
                    <div class="search-pill flex-1 !py-2.5">
                        <Search class="w-5 h-5 text-secondary shrink-0" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Поиск по названию, OEM-номеру или артикулу..."
                            class="flex-1 bg-transparent border-none outline-none text-sm"
                        />
                    </div>
                    <Button type="submit">Найти</Button>
                </form>
            </div>

            <div v-if="products.data?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
            </div>
            <div v-else class="surface-panel text-center py-20">
                <p class="text-on-surface-muted">Товары не найдены.</p>
            </div>
        </div>
    </AppLayout>
</template>
