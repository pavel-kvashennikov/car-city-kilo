<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import VehicleCard from '@/Components/Catalog/VehicleCard.vue';
import ProductCard from '@/Components/Catalog/ProductCard.vue';
import ServiceCard from '@/Components/Catalog/ServiceCard.vue';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, Phone, Mail, Globe, MapPin, Car, Package, Wrench } from 'lucide-vue-next';

defineProps({
    company: { type: Object, required: true },
    vehicles: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    services: { type: Array, default: () => [] },
});

const profileLabel = { dealer: 'Дилер', parts: 'Запчасти', service: 'Сервис' };
const profileBadge = { dealer: 'badge-primary', parts: 'badge-success', service: 'badge-warning' };
</script>

<template>
    <AppLayout>
        <div class="container-app py-6 space-y-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-on-surface-muted">
                <Link href="/companies" class="hover:text-primary flex items-center gap-1">
                    <ChevronLeft class="w-3.5 h-3.5" /> Компании
                </Link>
                <span>/</span>
                <span class="text-on-surface">{{ company.name }}</span>
            </div>

            <!-- Company header -->
            <div class="card p-6">
                <div class="flex items-start gap-5">
                    <div v-if="company.logo_path" class="w-16 h-16 rounded-xl overflow-hidden shrink-0 ring-2 ring-outline">
                        <img :src="company.logo_path" :alt="company.name" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shrink-0 shadow-sm">
                        <span class="text-white font-bold text-2xl">{{ company.name?.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <h1 class="font-extrabold text-xl text-on-surface">{{ company.name }}</h1>
                            <div v-if="company.business_profiles?.length" class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="p in company.business_profiles"
                                    :key="p.id"
                                    :class="['badge', profileBadge[p.type || p.profile_type] || 'badge-neutral']"
                                >{{ profileLabel[p.type || p.profile_type] || p.type || p.profile_type }}</span>
                            </div>
                        </div>
                        <p v-if="company.description" class="text-sm text-on-surface-muted leading-relaxed mb-3">{{ company.description }}</p>
                        <div class="flex flex-wrap gap-4 text-sm text-on-surface-muted">
                            <span v-if="company.phone" class="flex items-center gap-1.5">
                                <Phone class="w-3.5 h-3.5 text-primary" /> {{ company.phone }}
                            </span>
                            <span v-if="company.email" class="flex items-center gap-1.5">
                                <Mail class="w-3.5 h-3.5 text-primary" /> {{ company.email }}
                            </span>
                            <a v-if="company.website" :href="company.website" target="_blank" class="flex items-center gap-1.5 text-primary hover:underline">
                                <Globe class="w-3.5 h-3.5" /> {{ company.website }}
                            </a>
                            <span v-if="company.address" class="flex items-center gap-1.5">
                                <MapPin class="w-3.5 h-3.5 text-primary" /> {{ company.address }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicles -->
            <section v-if="vehicles.length">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="page-title flex items-center gap-2">
                        <Car class="w-5 h-5 text-primary" /> Автомобили
                    </h2>
                    <Link href="/catalog/cars" class="btn-secondary !text-xs !py-1.5">Все авто</Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <VehicleCard v-for="v in vehicles" :key="v.id" :vehicle="v" />
                </div>
            </section>

            <!-- Products -->
            <section v-if="products.length">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="page-title flex items-center gap-2">
                        <Package class="w-5 h-5 text-primary" /> Запчасти
                    </h2>
                    <Link href="/catalog/parts" class="btn-secondary !text-xs !py-1.5">Все запчасти</Link>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    <ProductCard v-for="p in products" :key="p.id" :product="p" />
                </div>
            </section>

            <!-- Services -->
            <section v-if="services?.length">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="page-title flex items-center gap-2">
                        <Wrench class="w-5 h-5 text-primary" /> Сервисы
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <ServiceCard v-for="s in services" :key="s.id" :service="s" />
                </div>
            </section>

            <!-- Empty state -->
            <div v-if="!vehicles.length && !products.length && !services?.length" class="card text-center py-16">
                <div class="text-4xl mb-3">🏢</div>
                <p class="font-semibold text-on-surface mb-1">Информация обновляется</p>
                <p class="text-sm text-on-surface-muted">Компания ещё не разместила объявления</p>
            </div>
        </div>
    </AppLayout>
</template>
