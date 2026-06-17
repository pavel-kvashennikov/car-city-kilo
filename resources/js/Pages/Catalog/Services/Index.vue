<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({ services: Object, filters: Object });
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold mb-8">Автосервисы</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link
                    v-for="service in services.data"
                    :key="service.id"
                    :href="`/catalog/services/${service.slug}`"
                    class="bg-white rounded-xl border p-6 hover:shadow-md transition-shadow"
                >
                    <h3 class="font-semibold text-lg">{{ service.company?.name }}</h3>
                    <p v-if="service.description" class="text-gray-600 text-sm mt-2 line-clamp-3">{{ service.description }}</p>
                    <div v-if="service.specializations?.length" class="flex flex-wrap gap-1 mt-3">
                        <span v-for="spec in service.specializations" :key="spec" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ spec }}</span>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
