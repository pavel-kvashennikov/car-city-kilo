<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Car, Wrench, Settings, MapPin, ArrowRight, Shield, Zap, Users,
    Star, ChevronRight, Package, CheckCircle2, TrendingUp
} from 'lucide-vue-next';

const props = defineProps({
    stats: { type: Object, default: () => ({ vehicles: 0, parts: 0, services: 0, companies: 0 }) },
    featuredVehicles: { type: Array, default: () => [] },
    featuredParts: { type: Array, default: () => [] },
    featuredServices: { type: Array, default: () => [] },
});

const fmt = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
const km = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' км';

const categories = [
    {
        icon: Car, title: 'Автомобили',
        desc: 'Новые и с пробегом от проверенных дилеров площадки',
        href: '/catalog/cars',
        iconBg: 'bg-gradient-to-br from-blue-500 to-blue-700',
        pill: 'badge-primary',
    },
    {
        icon: Settings, title: 'Запчасти',
        desc: 'Оригинал и аналоги с поиском по OEM-номеру',
        href: '/catalog/parts',
        iconBg: 'bg-gradient-to-br from-teal-500 to-teal-700',
        pill: 'badge-success',
    },
    {
        icon: Wrench, title: 'Автосервис',
        desc: 'Онлайн-запись к мастерам резидентов рынка',
        href: '/catalog/services',
        iconBg: 'bg-gradient-to-br from-amber-500 to-orange-600',
        pill: 'badge-warning',
    },
    {
        icon: MapPin, title: 'Карта рынка',
        desc: 'Интерактивная схема — найдите нужный павильон',
        href: '/market-map',
        iconBg: 'bg-gradient-to-br from-violet-500 to-purple-700',
        pill: 'badge-neutral',
    },
];

const highlights = [
    { icon: Shield, text: 'Проверенные резиденты' },
    { icon: Zap, text: 'Быстрый поиск по OEM' },
    { icon: Users, text: 'Единая точка входа' },
    { icon: CheckCircle2, text: 'Гарантия юр. чистоты' },
];
</script>

<template>
    <AppLayout>

        <!-- ══════════════ HERO ══════════════ -->
        <section class="hero-light py-12 lg:py-20">
            <div class="container-app">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

                    <!-- Copy -->
                    <div>
                        <div class="eyebrow mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary-bright animate-pulse" />
                            Городской авторынок онлайн
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight text-on-surface mb-4">
                            Всё для вашего авто
                            <span class="block text-gradient">в одном месте</span>
                        </h1>

                        <p class="text-base text-on-surface-muted leading-relaxed mb-8 max-w-lg">
                            Авто, запчасти и сервис от резидентов площадки.
                            Сравнивайте, выбирайте и покупайте без лишних поездок.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 mb-8">
                            <Link href="/catalog/cars" class="btn-primary !py-2.5 !px-5 !text-sm">
                                <Car class="w-4 h-4" />
                                Смотреть автомобили
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                            <Link href="/register/company" class="btn-secondary !py-2.5 !px-5 !text-sm">
                                Стать резидентом
                            </Link>
                        </div>

                        <div class="flex flex-wrap gap-x-5 gap-y-2">
                            <div v-for="item in highlights" :key="item.text"
                                class="flex items-center gap-1.5 text-xs text-on-surface-muted">
                                <component :is="item.icon" class="w-3.5 h-3.5 text-primary" />
                                {{ item.text }}
                            </div>
                        </div>
                    </div>

                    <!-- Stats grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            v-for="(s, key) in [
                                { label: 'Автомобилей', value: stats.vehicles, icon: Car, gradient: 'from-blue-500 to-blue-700' },
                                { label: 'Запчастей', value: stats.parts, icon: Package, gradient: 'from-teal-500 to-teal-700' },
                                { label: 'Сервисов', value: stats.services, icon: Wrench, gradient: 'from-amber-500 to-orange-600' },
                                { label: 'Компаний', value: stats.companies, icon: Users, gradient: 'from-violet-500 to-purple-700' },
                            ]"
                            :key="s.label"
                            class="card p-5 flex items-center gap-4"
                        >
                            <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center shrink-0', s.gradient]">
                                <component :is="s.icon" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <div class="text-2xl font-extrabold text-on-surface">{{ s.value }}+</div>
                                <div class="text-xs text-on-surface-muted mt-0.5">{{ s.label }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════ CATEGORIES ══════════════ -->
        <section class="section bg-surface-muted">
            <div class="container-app">
                <div class="flex items-end justify-between mb-7">
                    <div>
                        <p class="eyebrow mb-1.5">Что вы найдёте</p>
                        <h2 class="page-title">Четыре направления — один портал</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <Link
                        v-for="cat in categories"
                        :key="cat.title"
                        :href="cat.href"
                        class="card card-hover group p-5"
                    >
                        <div :class="[cat.iconBg, 'w-11 h-11 rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:scale-105 transition-transform']">
                            <component :is="cat.icon" class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="font-bold text-sm text-on-surface mb-1.5">{{ cat.title }}</h3>
                        <p class="text-xs text-on-surface-muted leading-relaxed mb-3">{{ cat.desc }}</p>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-primary">
                            Перейти <ChevronRight class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ══════════════ FEATURED VEHICLES ══════════════ -->
        <section v-if="featuredVehicles.length" class="section">
            <div class="container-app">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <p class="eyebrow mb-1.5">Каталог авто</p>
                        <h2 class="page-title">Популярные автомобили</h2>
                    </div>
                    <Link href="/catalog/cars" class="btn-secondary !py-1.5 !text-xs hidden sm:inline-flex">
                        Все объявления <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link
                        v-for="vehicle in featuredVehicles"
                        :key="vehicle.id"
                        :href="`/catalog/cars/${vehicle.slug}`"
                        class="card card-hover overflow-hidden group block"
                    >
                        <!-- Photo -->
                        <div class="relative aspect-[16/10] bg-surface-muted overflow-hidden">
                            <img
                                v-if="vehicle.photos?.[0]?.path"
                                :src="vehicle.photos[0].path"
                                :alt="`${vehicle.brand?.name} ${vehicle.car_model?.name}`"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-surface-dim">
                                <Car class="w-10 h-10 text-outline" />
                            </div>
                            <!-- Badges -->
                            <div class="absolute top-2.5 left-2.5 flex gap-1.5">
                                <span v-if="vehicle.condition === 'new'" class="badge badge-success">Новый</span>
                                <span v-if="vehicle.is_featured" class="badge badge-primary">
                                    <Star class="w-2.5 h-2.5" /> Топ
                                </span>
                            </div>
                            <div class="absolute top-2.5 right-2.5">
                                <span class="badge badge-neutral">{{ vehicle.year }}</span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors truncate">
                                {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1.5 text-xs text-on-surface-muted">
                                <span v-if="vehicle.mileage > 0">{{ km(vehicle.mileage) }}</span>
                                <span v-else>Новый</span>
                                <span class="text-outline">·</span>
                                <span v-if="vehicle.transmission === 'automatic'">Автомат</span>
                                <span v-else-if="vehicle.transmission === 'manual'">Механика</span>
                                <span v-else>{{ vehicle.transmission }}</span>
                                <span class="text-outline">·</span>
                                <span>{{ vehicle.engine_volume }} л</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-extrabold text-gradient">{{ fmt(vehicle.price) }}</span>
                                <span class="text-xs text-on-surface-muted truncate max-w-[110px]">
                                    {{ vehicle.dealer_profile?.company?.name || '' }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div class="mt-5 text-center sm:hidden">
                    <Link href="/catalog/cars" class="btn-secondary !text-sm">
                        Все автомобили <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- ══════════════ FEATURED PARTS ══════════════ -->
        <section v-if="featuredParts.length" class="section bg-surface-muted">
            <div class="container-app">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <p class="eyebrow mb-1.5">Запчасти</p>
                        <h2 class="page-title">В наличии сегодня</h2>
                    </div>
                    <Link href="/catalog/parts" class="btn-secondary !py-1.5 !text-xs hidden sm:inline-flex">
                        Все запчасти <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <Link
                        v-for="product in featuredParts.slice(0, 8)"
                        :key="product.id"
                        :href="`/catalog/parts/${product.slug}`"
                        class="card card-hover overflow-hidden group block"
                    >
                        <div class="relative aspect-square bg-surface-muted overflow-hidden">
                            <img
                                v-if="product.cover_photo_url || product.attributes?.images?.[0]"
                                :src="product.cover_photo_url || product.attributes?.images?.[0]"
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center bg-surface-dim">
                                <Settings class="w-8 h-8 text-outline" />
                            </div>
                            <div class="absolute top-2 right-2">
                                <span :class="['badge', product.stock_quantity > 0 ? 'badge-success' : 'badge-neutral']">
                                    {{ product.stock_quantity > 0 ? 'В наличии' : 'Под заказ' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="font-semibold text-xs text-on-surface line-clamp-2 group-hover:text-primary transition-colors leading-relaxed">
                                {{ product.name }}
                            </h3>
                            <p v-if="product.oem_number" class="text-xs text-on-surface-muted mt-1 font-mono">
                                {{ product.oem_number }}
                            </p>
                            <p class="text-sm font-extrabold text-gradient mt-2">
                                {{ fmt(product.price_retail) }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ══════════════ FEATURED SERVICES ══════════════ -->
        <section v-if="featuredServices.length" class="section">
            <div class="container-app">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <p class="eyebrow mb-1.5">Сервисы</p>
                        <h2 class="page-title">Рекомендуемые сервисы</h2>
                    </div>
                    <Link href="/catalog/services" class="btn-secondary !py-1.5 !text-xs hidden sm:inline-flex">
                        Все сервисы <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link
                        v-for="service in featuredServices"
                        :key="service.id"
                        :href="`/catalog/services/${service.slug}`"
                        class="card card-hover group block p-5"
                    >
                        <div class="flex items-center gap-4 mb-4">
                            <div v-if="service.company?.logo_path" class="w-12 h-12 rounded-xl overflow-hidden shrink-0 ring-2 ring-outline">
                                <img :src="`/storage/${service.company.logo_path}`" :alt="service.company.name" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shrink-0 shadow-sm">
                                <span class="text-white font-bold text-lg">{{ service.company?.name?.charAt(0) }}</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors truncate">
                                    {{ service.company?.name }}
                                </h3>
                                <p class="text-xs text-on-surface-muted mt-0.5 line-clamp-1">
                                    {{ service.description }}
                                </p>
                            </div>
                        </div>
                        <div v-if="service.specializations?.length" class="flex flex-wrap gap-1.5 mb-3">
                            <span
                                v-for="spec in service.specializations.slice(0, 3)"
                                :key="spec"
                                class="badge badge-neutral"
                            >{{ spec }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-on-surface-muted border-t border-outline pt-3">
                            <span class="flex items-center gap-1">
                                <Wrench class="w-3 h-3 text-primary" />
                                Онлайн-запись
                            </span>
                            <ChevronRight class="w-3.5 h-3.5 text-primary group-hover:translate-x-0.5 transition-transform" />
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ══════════════ CTA — Become resident ══════════════ -->
        <section class="section bg-surface-muted">
            <div class="container-app">
                <div class="card overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <!-- Dark left -->
                        <div class="bg-gradient-to-br from-carbon-deep via-carbon to-blue-950 p-8 lg:p-12 text-white">
                            <p class="eyebrow mb-4 !text-blue-300">Для бизнеса</p>
                            <h2 class="text-2xl lg:text-3xl font-extrabold leading-tight mb-4">
                                Станьте резидентом авторынка
                            </h2>
                            <p class="text-gray-400 text-sm leading-relaxed mb-6">
                                Разместите автомобили, запчасти или услуги и получайте клиентов онлайн.
                                Стартовая настройка меньше 30 минут.
                            </p>
                            <div class="flex gap-3">
                                <Link href="/register/company" class="btn-primary !text-sm">
                                    <Users class="w-4 h-4" /> Зарегистрироваться
                                </Link>
                                <Link href="/cabinet/billing" class="btn-secondary !text-sm border-white/20 !text-gray-300 hover:!text-white hover:!bg-white/10">
                                    Тарифы
                                </Link>
                            </div>
                        </div>
                        <!-- Benefits right -->
                        <div class="p-8 lg:p-12 grid grid-cols-1 sm:grid-cols-2 gap-4 content-center">
                            <div v-for="(b, i) in [
                                { icon: TrendingUp, title: 'Больше продаж', desc: 'Доступ к тысячам клиентов онлайн' },
                                { icon: Zap, title: 'Простой кабинет', desc: 'Управляйте объявлениями и заявками' },
                                { icon: Shield, title: 'Надёжно', desc: 'Проверенная платформа для резидентов' },
                                { icon: MapPin, title: 'Карта рынка', desc: 'Ваше место на интерактивной схеме' },
                            ]" :key="i" class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-light flex items-center justify-center shrink-0">
                                    <component :is="b.icon" class="w-4 h-4 text-primary" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-on-surface">{{ b.title }}</p>
                                    <p class="text-xs text-on-surface-muted mt-0.5">{{ b.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </AppLayout>
</template>
