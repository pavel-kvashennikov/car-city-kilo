<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Car, Wrench, Settings, MapPin, ArrowRight, Shield, Zap, Users } from 'lucide-vue-next';

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
        description: 'Новые и с пробегом от проверенных дилеров площадки',
        href: '/catalog/cars',
        accent: 'from-blue-500/15 to-blue-600/5',
        iconBg: 'bg-gradient-to-br from-primary-bright to-primary',
    },
    {
        icon: Settings,
        title: 'Запчасти',
        description: 'Оригинал и аналоги с умным поиском по OEM-номеру',
        href: '/catalog/parts',
        accent: 'from-emerald-500/15 to-emerald-600/5',
        iconBg: 'bg-gradient-to-br from-tertiary-bright to-tertiary',
    },
    {
        icon: Wrench,
        title: 'Автосервис',
        description: 'Онлайн-запись к мастерам резидентов рынка',
        href: '/catalog/services',
        accent: 'from-amber-500/15 to-amber-600/5',
        iconBg: 'bg-gradient-to-br from-amber-500 to-orange-600',
    },
    {
        icon: MapPin,
        title: 'Карта рынка',
        description: 'Интерактивная схема — найдите нужный павильон',
        href: '/market-map',
        accent: 'from-violet-500/15 to-violet-600/5',
        iconBg: 'bg-gradient-to-br from-violet-500 to-purple-600',
    },
];

const highlights = [
    { icon: Shield, text: 'Проверенные резиденты' },
    { icon: Zap, text: 'Быстрый поиск по OEM' },
    { icon: Users, text: 'Единая точка входа' },
];
</script>

<template>
    <AppLayout>
        <!-- Hero -->
        <section class="hero-gradient text-white">
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-sm font-medium mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                        Городской авторынок онлайн
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-6">
                        Всё для вашего авто
                        <span class="block text-primary-muted">в одном месте</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-blue-100/90 leading-relaxed mb-10 max-w-2xl">
                        Автомобили, запчасти и сервис от резидентов площадки.
                        Сравнивайте, выбирайте и покупайте без лишних поездок.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link href="/catalog/cars" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base font-semibold rounded-xl bg-white text-primary hover:bg-blue-50 shadow-elevated transition-all hover:-translate-y-0.5">
                            Смотреть автомобили
                            <ArrowRight class="w-5 h-5" />
                        </Link>
                        <Link href="/register/company" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold rounded-xl border-2 border-white/30 text-white hover:bg-white/10 backdrop-blur-sm transition-all">
                            Стать резидентом
                        </Link>
                    </div>
                    <div class="flex flex-wrap gap-6 mt-10 pt-8 border-t border-white/15">
                        <div v-for="item in highlights" :key="item.text" class="flex items-center gap-2 text-sm text-blue-100/80">
                            <component :is="item.icon" class="w-4 h-4 text-primary-muted" />
                            {{ item.text }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-20 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="page-title">Что вы найдёте на площадке</h2>
                    <p class="page-subtitle max-w-xl mx-auto">Четыре направления — один портал для покупателей и бизнеса</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Link
                        v-for="feature in features"
                        :key="feature.title"
                        :href="feature.href"
                        class="card card-hover card-featured p-6 group"
                        :class="`bg-gradient-to-br ${feature.accent}`"
                    >
                        <div :class="[feature.iconBg, 'w-12 h-12 rounded-xl flex items-center justify-center mb-5 shadow-md group-hover:scale-105 transition-transform']">
                            <component :is="feature.icon" class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-on-surface mb-2">{{ feature.title }}</h3>
                        <p class="text-on-surface-muted text-sm leading-relaxed">{{ feature.description }}</p>
                        <span class="inline-flex items-center gap-1 mt-4 text-sm font-semibold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                            Перейти <ArrowRight class="w-4 h-4" />
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="surface-panel p-8 lg:p-12">
                    <div class="text-center mb-10">
                        <h2 class="page-title">Площадка в цифрах</h2>
                        <p class="page-subtitle">Растущая экосистема резидентов и предложений</p>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                        <div class="stat-card">
                            <div class="text-3xl lg:text-4xl font-extrabold text-gradient">{{ stats.vehicles }}+</div>
                            <div class="text-on-surface-muted mt-2 text-sm font-medium">Автомобилей</div>
                        </div>
                        <div class="stat-card">
                            <div class="text-3xl lg:text-4xl font-extrabold text-gradient">{{ stats.parts }}+</div>
                            <div class="text-on-surface-muted mt-2 text-sm font-medium">Запчастей</div>
                        </div>
                        <div class="stat-card">
                            <div class="text-3xl lg:text-4xl font-extrabold text-gradient">{{ stats.services }}+</div>
                            <div class="text-on-surface-muted mt-2 text-sm font-medium">Сервисов</div>
                        </div>
                        <div class="stat-card">
                            <div class="text-3xl lg:text-4xl font-extrabold text-gradient">{{ stats.companies }}+</div>
                            <div class="text-on-surface-muted mt-2 text-sm font-medium">Компаний</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
