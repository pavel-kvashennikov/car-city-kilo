<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import ServiceCard from '@/Components/Catalog/ServiceCard.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Search, X } from 'lucide-vue-next';

const props = defineProps({ services: Object, filters: Object });

const form = ref({ ...props.filters });

function applyFilters() {
    const params = Object.fromEntries(Object.entries(form.value).filter(([, v]) => v !== undefined && v !== ''));
    router.get('/catalog/services', params, { preserveState: true, preserveScroll: true });
}
</script>

<template>
    <AppLayout>
        <div class="container-app py-7">
            <div class="flex items-start justify-between mb-6 gap-4">
                <div>
                    <h1 class="page-title">Автосервисы</h1>
                    <p class="page-subtitle">Онлайн-запись к мастерам резидентов. Найдено: {{ services.total ?? services.data?.length ?? 0 }}</p>
                </div>
            </div>

            <!-- Search -->
            <div class="card p-4 mb-6">
                <div class="flex gap-3">
                    <div class="search-pill flex-1 !py-2">
                        <Search class="w-4 h-4 text-secondary shrink-0" />
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Название сервиса или специализация..."
                            class="flex-1 bg-transparent border-none outline-none text-sm"
                            @keyup.enter="applyFilters"
                        />
                        <button v-if="form.search" @click="form.search = ''; applyFilters()">
                            <X class="w-4 h-4 text-secondary hover:text-danger" />
                        </button>
                    </div>
                    <button @click="applyFilters" class="btn-primary !text-sm">Найти</button>
                </div>
            </div>

            <!-- Grid -->
            <div v-if="services.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <ServiceCard v-for="service in services.data" :key="service.id" :service="service" />
            </div>

            <div v-else class="card text-center py-16">
                <div class="text-4xl mb-3">🔧</div>
                <p class="font-semibold text-on-surface mb-1">Сервисы не найдены</p>
                <p class="text-sm text-on-surface-muted">Попробуйте другой запрос</p>
            </div>

            <!-- Pagination -->
            <div v-if="services.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
                <Link
                    v-for="page in services.links"
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
    </AppLayout>
</template>
