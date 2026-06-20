<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Building2, Car, Package, Wrench, ChevronRight } from 'lucide-vue-next';

defineProps({
    companies: { type: Object, default: () => ({ data: [], links: [] }) },
});

const profileIcon = { dealer: Car, parts: Package, service: Wrench };
const profileLabel = { dealer: 'Дилер', parts: 'Запчасти', service: 'Сервис' };
const profileBadge = { dealer: 'badge-primary', parts: 'badge-success', service: 'badge-warning' };
</script>

<template>
    <AppLayout>
        <div class="container-app py-7">
            <div class="mb-7">
                <h1 class="page-title">Компании</h1>
                <p class="page-subtitle">Резиденты авторынка — дилеры, запчасти и сервисы</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Link
                    v-for="company in companies.data"
                    :key="company.id"
                    :href="`/companies/${company.slug}`"
                    class="card card-hover group block p-5"
                >
                    <div class="flex items-center gap-4 mb-4">
                        <div v-if="company.logo_path" class="w-12 h-12 rounded-xl overflow-hidden shrink-0 ring-2 ring-outline">
                            <img :src="`/storage/${company.logo_path}`" :alt="company.name" class="w-full h-full object-cover" />
                        </div>
                        <div v-else class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shrink-0 shadow-sm">
                            <span class="text-white font-bold text-lg">{{ company.name?.charAt(0) }}</span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors truncate">
                                {{ company.name }}
                            </h3>
                            <div v-if="company.business_profiles?.length" class="flex flex-wrap gap-1 mt-1">
                                <span
                                    v-for="profile in company.business_profiles"
                                    :key="profile.id"
                                    :class="['badge', profileBadge[profile.type || profile.profile_type] || 'badge-neutral']"
                                >
                                    <component :is="profileIcon[profile.type || profile.profile_type] || Building2" class="w-2.5 h-2.5" />
                                    {{ profileLabel[profile.type || profile.profile_type] || profile.type || profile.profile_type }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <p v-if="company.description" class="text-xs text-on-surface-muted leading-relaxed line-clamp-2 mb-3">
                        {{ company.description }}
                    </p>
                    <div class="flex items-center gap-3 text-xs text-on-surface-muted pt-3 border-t border-outline">
                        <span v-if="company.phone">{{ company.phone }}</span>
                        <span class="ml-auto flex items-center gap-0.5 text-primary group-hover:gap-1.5 transition-all font-semibold">
                            Подробнее <ChevronRight class="w-3 h-3" />
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="companies.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
                <Link
                    v-for="page in companies.links"
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
