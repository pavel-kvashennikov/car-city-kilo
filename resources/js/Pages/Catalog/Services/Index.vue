<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import ServiceCard from '@/Components/Catalog/ServiceCard.vue';

defineProps({ services: Object, filters: Object });
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-8">
                <h1 class="page-title">Автосервисы</h1>
                <p class="page-subtitle">Запись онлайн к мастерам резидентов рынка</p>
            </div>
            <div v-if="services.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <ServiceCard v-for="service in services.data" :key="service.id" :service="service" />
            </div>
            <div v-else class="surface-panel text-center py-20">
                <p class="text-on-surface-muted">Сервисы не найдены.</p>
            </div>
        </div>
    </AppLayout>
</template>
