<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';

defineProps({ product: Object });
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-xl border p-8">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
                        <h1 class="text-2xl font-bold mt-2">{{ product.name }}</h1>
                        <p v-if="product.oem_number" class="mt-2 text-gray-600">OEM: <span class="font-mono">{{ product.oem_number }}</span></p>
                        <p v-if="product.article_number" class="text-gray-600">Артикул: {{ product.article_number }}</p>
                        <p v-if="product.brand" class="text-gray-600">Бренд: {{ product.brand }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-green-600">{{ Number(product.price_retail).toLocaleString() }} ₽</div>
                        <div v-if="product.price_wholesale" class="text-sm text-gray-500 mt-1">Опт: {{ Number(product.price_wholesale).toLocaleString() }} ₽ (от {{ product.wholesale_min_qty }} шт.)</div>
                    </div>
                </div>

                <div v-if="product.description" class="mt-6 pt-6 border-t">
                    <h2 class="font-semibold mb-2">Описание</h2>
                    <p class="text-gray-700">{{ product.description }}</p>
                </div>

                <div v-if="product.cross_numbers?.length" class="mt-6 pt-6 border-t">
                    <h2 class="font-semibold mb-2">Кросс-номера</h2>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="cn in product.cross_numbers" :key="cn.id" class="px-3 py-1 bg-gray-100 rounded text-sm font-mono">
                            {{ cn.brand ? `${cn.brand}: ` : '' }}{{ cn.number }}
                        </span>
                    </div>
                </div>

                <div v-if="product.applicabilities?.length" class="mt-6 pt-6 border-t">
                    <h2 class="font-semibold mb-2">Применимость</h2>
                    <table class="w-full text-sm">
                        <thead><tr class="border-b"><th class="text-left py-2">Марка</th><th class="text-left">Модель</th><th>Годы</th></tr></thead>
                        <tbody>
                            <tr v-for="app in product.applicabilities" :key="app.id" class="border-b">
                                <td class="py-2">{{ app.brand?.name }}</td>
                                <td>{{ app.car_model?.name || '—' }}</td>
                                <td class="text-center">{{ app.year_from || '...' }} – {{ app.year_to || '...' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    <button class="px-8 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                        В корзину
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
