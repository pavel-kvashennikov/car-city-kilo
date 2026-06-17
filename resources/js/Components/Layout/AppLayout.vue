<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Menu, X, ShoppingCart, User, Search } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const mobileMenuOpen = ref(false);

const navigation = [
    { name: 'Автомобили', href: '/catalog/cars' },
    { name: 'Запчасти', href: '/catalog/parts' },
    { name: 'Сервисы', href: '/catalog/services' },
    { name: 'Компании', href: '/companies' },
    { name: 'Карта рынка', href: '/market-map' },
];
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="text-xl font-bold text-blue-600">
                            AutoMarket
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors"
                        >
                            {{ item.name }}
                        </Link>
                    </nav>

                    <!-- Right section -->
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">
                            <Search class="w-5 h-5" />
                        </button>
                        <Link href="/cart" class="text-gray-500 hover:text-gray-700 relative">
                            <ShoppingCart class="w-5 h-5" />
                        </Link>

                        <template v-if="user">
                            <Link href="/cabinet" class="text-gray-500 hover:text-gray-700">
                                <User class="w-5 h-5" />
                            </Link>
                        </template>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                Войти
                            </Link>
                            <Link href="/register" class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition-colors">
                                Регистрация
                            </Link>
                        </template>

                        <!-- Mobile menu button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-500">
                            <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
                            <X v-else class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md"
                        @click="mobileMenuOpen = false"
                    >
                        {{ item.name }}
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-bold mb-4">AutoMarket</h3>
                        <p class="text-gray-400 text-sm">
                            Единый портал городского авторынка. Автомобили, запчасти и сервис в одном месте.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-3">Каталог</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><Link href="/catalog/cars" class="hover:text-white">Автомобили</Link></li>
                            <li><Link href="/catalog/parts" class="hover:text-white">Запчасти</Link></li>
                            <li><Link href="/catalog/services" class="hover:text-white">Автосервисы</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-3">Для бизнеса</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><Link href="/register/company" class="hover:text-white">Стать резидентом</Link></li>
                            <li><Link href="/pricing" class="hover:text-white">Тарифы</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-3">Контакты</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li>Телефон: +7 (XXX) XXX-XX-XX</li>
                            <li>Email: info@automarket.ru</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
                    &copy; {{ new Date().getFullYear() }} AutoMarket. Все права защищены.
                </div>
            </div>
        </footer>
    </div>
</template>
