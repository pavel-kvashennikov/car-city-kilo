<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold mb-6">Запчасти</h1>

            <!-- Search -->
            <div class="bg-white rounded-xl border p-6 mb-8">
                <form @submit.prevent="doSearch" class="flex gap-4">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Поиск по названию, OEM-номеру или артикулу..."
                        class="flex-1 border rounded-lg px-4 py-3"
                    />
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                        Найти
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <Link
                    v-for="product in products.data"
                    :key="product.id"
                    :href="`/catalog/parts/${product.slug}`"
                    class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow"
                >
                    <div class="text-xs text-gray-500 mb-2">{{ product.category?.name }}</div>
                    <h3 class="font-semibold text-gray-900 line-clamp-2">{{ product.name }}</h3>
                    <p v-if="product.oem_number" class="text-sm text-gray-500 mt-1">OEM: {{ product.oem_number }}</p>
                    <p v-if="product.brand" class="text-sm text-gray-500">Бренд: {{ product.brand }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-lg font-bold text-green-600">{{ Number(product.price_retail).toLocaleString() }} ₽</span>
                        <span v-if="product.stock_quantity > 0" class="text-xs text-green-600">В наличии</span>
                        <span v-else class="text-xs text-red-500">Нет в наличии</span>
                    </div>
                </Link>
            </div>

            <div v-if="!products.data?.length" class="text-center py-20 text-gray-500">
                Товары не найдены.
            </div>
        </div>
    </AppLayout>
</template>
