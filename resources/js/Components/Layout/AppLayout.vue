<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Menu, X, ShoppingCart, User, Search, Car } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const mobileMenuOpen = ref(false);
const currentUrl = computed(() => page.url);

const navigation = [
    { name: 'Автомобили', href: '/catalog/cars' },
    { name: 'Запчасти', href: '/catalog/parts' },
    { name: 'Сервисы', href: '/catalog/services' },
    { name: 'Компании', href: '/companies' },
    { name: 'Карта рынка', href: '/market-map' },
];

const isActive = (href) => currentUrl.value.startsWith(href);
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <header class="header-glass sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-8">
                        <Link href="/" class="flex items-center gap-2.5 group">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                                <Car class="w-5 h-5 text-white" />
                            </div>
                            <span class="text-xl font-bold text-gradient">AutoMarket</span>
                        </Link>

                        <nav class="hidden lg:flex items-center gap-1">
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

                    <div class="flex items-center gap-2">
                        <div class="hidden md:flex search-pill">
                            <Search class="w-4 h-4 text-secondary shrink-0" />
                            <input
                                type="text"
                                placeholder="Поиск..."
                                class="bg-transparent border-none outline-none text-sm w-36 lg:w-44 placeholder:text-secondary/60"
                            />
                        </div>

                        <Link href="/cart" class="btn-ghost relative">
                            <ShoppingCart class="w-5 h-5" />
                        </Link>

                        <template v-if="user">
                            <Link href="/buyer/dashboard" class="btn-ghost">
                                <User class="w-5 h-5" />
                            </Link>
                        </template>
                        <template v-else>
                            <Link href="/login" class="hidden sm:inline-flex nav-link">
                                Войти
                            </Link>
                            <Link href="/register" class="btn-primary text-sm !py-2 !px-4">
                                Регистрация
                            </Link>
                        </template>

                        <button class="lg:hidden btn-ghost" @click="mobileMenuOpen = !mobileMenuOpen">
                            <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                            <X v-else class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="mobileMenuOpen" class="lg:hidden border-t border-outline/30 bg-white/95 backdrop-blur-xl">
                <div class="px-4 py-3 space-y-1">
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
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="bg-carbon-deep text-white mt-auto relative overflow-hidden">
            <div class="h-px bg-gradient-to-r from-transparent via-primary-bright/40 to-transparent" />
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div class="md:col-span-1">
                        <div class="flex items-center gap-2.5 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                                <Car class="w-4 h-4 text-white" />
                            </div>
                            <span class="text-lg font-bold">AutoMarket</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Единый портал городского авторынка. Автомобили, запчасти и сервис от проверенных резидентов.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-300 mb-4">Каталог</h4>
                        <ul class="space-y-2.5 text-sm text-gray-400">
                            <li><Link href="/catalog/cars" class="hover:text-white transition-colors">Автомобили</Link></li>
                            <li><Link href="/catalog/parts" class="hover:text-white transition-colors">Запчасти</Link></li>
                            <li><Link href="/catalog/services" class="hover:text-white transition-colors">Автосервисы</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-300 mb-4">Для бизнеса</h4>
                        <ul class="space-y-2.5 text-sm text-gray-400">
                            <li><Link href="/register/company" class="hover:text-white transition-colors">Стать резидентом</Link></li>
                            <li><Link href="/cabinet/billing" class="hover:text-white transition-colors">Тарифы</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm uppercase tracking-wider text-gray-300 mb-4">Контакты</h4>
                        <ul class="space-y-2.5 text-sm text-gray-400">
                            <li>+7 (XXX) XXX-XX-XX</li>
                            <li>info@automarket.ru</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                    <span>&copy; {{ new Date().getFullYear() }} AutoMarket. Все права защищены.</span>
                    <span class="text-gray-600">Цифровой двойник авторынка</span>
                </div>
            </div>
        </footer>
    </div>
</template>
