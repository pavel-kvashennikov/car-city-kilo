<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Car, Wrench, Settings, MapPin } from 'lucide-vue-next';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            vehicles: 0,
            parts: 0,
            services: 0,
            companies: 0,
        }),
    },
});

const features = [
    {
        icon: Car,
        title: 'Автомобили',
        description: 'Новые и с пробегом от проверенных дилеров',
        href: '/catalog/cars',
        color: 'bg-blue-100 text-blue-600',
    },
    {
        icon: Settings,
        title: 'Запчасти',
        description: 'Оригинал и аналоги с поиском по OEM-номеру',
        href: '/catalog/parts',
        color: 'bg-green-100 text-green-600',
    },
    {
        icon: Wrench,
        title: 'Автосервис',
        description: 'Онлайн-запись к проверенным мастерам',
        href: '/catalog/services',
        color: 'bg-orange-100 text-orange-600',
    },
    {
        icon: MapPin,
        title: 'Карта рынка',
        description: 'Найдите нужный павильон на интерактивной карте',
        href: '/market-map',
        color: 'bg-purple-100 text-purple-600',
    },
];
</script>

<template>
    <AppLayout>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Городской авторынок — онлайн
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                        Автомобили, запчасти и сервис от проверенных резидентов в одном месте.
                        Находите, сравнивайте и покупайте без лишних поездок.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            href="/catalog/cars"
                            class="inline-flex items-center justify-center px-8 py-3 text-lg font-medium rounded-lg bg-white text-blue-600 hover:bg-gray-100 transition-colors"
                        >
                            Смотреть автомобили
                        </Link>
                        <Link
                            href="/register/company"
                            class="inline-flex items-center justify-center px-8 py-3 text-lg font-medium rounded-lg border-2 border-white text-white hover:bg-white/10 transition-colors"
                        >
                            Стать резидентом
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <Link
                        v-for="feature in features"
                        :key="feature.title"
                        :href="feature.href"
                        class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow border border-gray-100"
                    >
                        <div :class="[feature.color, 'w-12 h-12 rounded-lg flex items-center justify-center mb-4']">
                            <component :is="feature.icon" class="w-6 h-6" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-600 text-sm">{{ feature.description }}</p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="bg-white py-16 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Площадка в цифрах</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ stats.vehicles }}+</div>
                        <div class="text-gray-600 mt-1">Автомобилей</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ stats.parts }}+</div>
                        <div class="text-gray-600 mt-1">Запчастей</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ stats.services }}+</div>
                        <div class="text-gray-600 mt-1">Сервисов</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ stats.companies }}+</div>
                        <div class="text-gray-600 mt-1">Компаний</div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
