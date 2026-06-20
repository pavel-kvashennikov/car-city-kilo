<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Menu, X, ShoppingCart, User, Search, Car, MapPin, Wrench, Settings } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const mobileMenuOpen = ref(false);
const currentUrl = computed(() => page.url);

const navigation = [
    { name: 'Автомобили', href: '/catalog/cars', icon: Car },
    { name: 'Запчасти', href: '/catalog/parts', icon: Settings },
    { name: 'Сервисы', href: '/catalog/services', icon: Wrench },
    { name: 'Компании', href: '/companies', icon: null },
    { name: 'Карта рынка', href: '/market-map', icon: MapPin },
];

const isActive = (href) => currentUrl.value.startsWith(href);
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="header-glass sticky top-0 z-50">
            <div class="container-app">
                <div class="flex items-center justify-between h-14">
                    <!-- Logo -->
                    <div class="flex items-center gap-6">
                        <Link href="/" class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                <Car class="w-4 h-4 text-white" />
                            </div>
                            <span class="text-base font-bold text-gradient">AutoMarket</span>
                        </Link>

                        <nav class="hidden lg:flex items-center gap-0.5">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="nav-link"
                                :class="{ 'nav-link-active': isActive(item.href) }"
                            >
                                {{ item.name }}
                            </Link>
                        </nav>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-1.5">
                        <div class="hidden md:flex search-pill">
                            <Search class="w-3.5 h-3.5 text-secondary shrink-0" />
                            <input
                                type="text"
                                placeholder="Поиск..."
                                class="bg-transparent border-none outline-none text-sm w-32 lg:w-40 placeholder:text-secondary/60"
                            />
                        </div>

                        <Link href="/cart" class="btn-ghost relative p-2">
                            <ShoppingCart class="w-4.5 h-4.5" style="width:1.1rem;height:1.1rem" />
                        </Link>

                        <template v-if="user">
                            <Link href="/buyer/dashboard" class="btn-ghost p-2">
                                <User style="width:1.1rem;height:1.1rem" />
                            </Link>
                            <Link href="/cabinet" class="btn-primary text-xs !py-1.5 !px-3">
                                Кабинет
                            </Link>
                        </template>
                        <template v-else>
                            <Link href="/login" class="hidden sm:inline-flex nav-link text-sm">Войти</Link>
                            <Link href="/register" class="btn-primary text-xs !py-1.5 !px-3.5">Регистрация</Link>
                        </template>

                        <button class="lg:hidden btn-ghost p-2 ml-1" @click="mobileMenuOpen = !mobileMenuOpen">
                            <component :is="mobileMenuOpen ? X : Menu" style="width:1.2rem;height:1.2rem" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileMenuOpen" class="lg:hidden border-t border-outline bg-white/98 backdrop-blur-xl">
                <div class="container-app py-3 space-y-0.5">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="block nav-link"
                        :class="{ 'nav-link-active': isActive(item.href) }"
                        @click="mobileMenuOpen = false"
                    >
                        {{ item.name }}
                    </Link>
                    <div class="pt-3 border-t border-outline flex gap-2 mt-2">
                        <Link href="/login" class="btn-secondary text-sm flex-1 text-center">Войти</Link>
                        <Link href="/register" class="btn-primary text-sm flex-1 text-center">Регистрация</Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-carbon-deep text-white mt-auto">
            <div class="h-px bg-gradient-to-r from-transparent via-primary-bright/30 to-transparent" />
            <div class="container-app py-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="col-span-2 md:col-span-1">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-md bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                                <Car class="w-3.5 h-3.5 text-white" />
                            </div>
                            <span class="font-bold">AutoMarket</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Единый портал городского авторынка. Авто, запчасти и сервис от проверенных резидентов.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-xs uppercase tracking-wider text-gray-400 mb-3">Каталог</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li><Link href="/catalog/cars" class="hover:text-white transition-colors">Автомобили</Link></li>
                            <li><Link href="/catalog/parts" class="hover:text-white transition-colors">Запчасти</Link></li>
                            <li><Link href="/catalog/services" class="hover:text-white transition-colors">Автосервисы</Link></li>
                            <li><Link href="/market-map" class="hover:text-white transition-colors">Карта рынка</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-xs uppercase tracking-wider text-gray-400 mb-3">Бизнес</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li><Link href="/register/company" class="hover:text-white transition-colors">Стать резидентом</Link></li>
                            <li><Link href="/cabinet/billing" class="hover:text-white transition-colors">Тарифы</Link></li>
                            <li><Link href="/cabinet" class="hover:text-white transition-colors">Личный кабинет</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-xs uppercase tracking-wider text-gray-400 mb-3">Контакты</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li>+7 (495) 120-34-56</li>
                            <li>info@automarket.ru</li>
                            <li class="pt-1">
                                <span class="text-xs text-gray-600">Пн–Пт 9:00–20:00</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-600">
                    <span>&copy; {{ new Date().getFullYear() }} AutoMarket. Все права защищены.</span>
                    <span>Цифровой двойник авторынка города</span>
                </div>
            </div>
        </footer>
    </div>
</template>
