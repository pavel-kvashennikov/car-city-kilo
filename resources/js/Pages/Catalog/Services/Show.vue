<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';

defineProps({ service: Object });
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-xl border p-8">
                <h1 class="text-2xl font-bold">{{ service.company?.name }}</h1>
                <p v-if="service.description" class="mt-4 text-gray-700">{{ service.description }}</p>

                <div v-if="service.service_items?.length" class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Услуги</h2>
                    <div class="space-y-3">
                        <div v-for="item in service.service_items" :key="item.id" class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.duration_minutes" class="text-sm text-gray-500">~{{ item.duration_minutes }} мин</div>
                            </div>
                            <div class="text-right">
                                <div v-if="item.price_fixed" class="font-semibold">{{ Number(item.price_fixed).toLocaleString() }} ₽</div>
                                <div v-else-if="item.price_from" class="font-semibold">от {{ Number(item.price_from).toLocaleString() }} ₽</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button class="px-8 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                        Записаться на сервис
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
