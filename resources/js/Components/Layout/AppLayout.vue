<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Menu, X, ShoppingCart, User, Search, Car, MapPin, Wrench, Settings, LogOut, LayoutDashboard, Heart, CalendarCheck, Package, ChevronDown, Shield } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const company = computed(() => page.props.company);
const isTenant = computed(() => !!company.value);
const isAdmin = computed(() => page.props.auth?.isAdmin ?? false);

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const currentUrl = computed(() => page.url);

const navigation = [
    { name: 'Автомобили', href: '/catalog/cars', icon: Car },
    { name: 'Запчасти', href: '/catalog/parts', icon: Settings },
    { name: 'Сервисы', href: '/catalog/services', icon: Wrench },
    { name: 'Компании', href: '/companies', icon: null },
    { name: 'Карта рынка', href: '/market-map', icon: MapPin },
    { name: 'Блог', href: '/blog', icon: null },
];

const isActive = (href) => currentUrl.value.startsWith(href);

function logout() {
    userMenuOpen.value = false;
    router.post('/logout');
}
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
                            <span class="text-base font-bold text-gradient">Город машин</span>
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
                            <!-- User dropdown -->
                            <div class="relative">
                                <button
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-1.5 btn-ghost !px-2.5 !py-1.5 text-sm font-medium"
                                >
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ user.name?.charAt(0)?.toUpperCase() }}</span>
                                    </div>
                                    <span class="hidden sm:inline max-w-[7rem] truncate">{{ user.name }}</span>
                                    <ChevronDown class="w-3.5 h-3.5 text-on-surface-muted transition-transform" :class="{ 'rotate-180': userMenuOpen }" />
                                </button>

                                <!-- Overlay to close dropdown -->
                                <div v-if="userMenuOpen" class="fixed inset-0 z-40" @click="userMenuOpen = false" />

                            <!-- Dropdown panel -->
                                <Transition
                                    enter-active-class="transition duration-100 ease-out"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition duration-75 ease-in"
                                    leave-from-class="opacity-100 scale-100"
                                    leave-to-class="opacity-0 scale-95"
                                >
                                    <div
                                        v-if="userMenuOpen"
                                        class="absolute right-0 top-full mt-2 w-56 card shadow-lg py-1 z-50 origin-top-right"
                                    >
                                        <div class="px-4 py-3 border-b border-outline">
                                            <p class="font-semibold text-sm text-on-surface truncate">{{ user.name }}</p>
                                            <p class="text-xs text-on-surface-muted truncate">{{ user.email }}</p>
                                        </div>

                                        <!-- Admin shortcut -->
                                        <div v-if="isAdmin" class="py-1 border-b border-outline">
                                            <Link href="/admin" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm font-semibold text-primary hover:bg-primary/5 transition-colors">
                                                <Shield class="w-4 h-4" /> Панель администратора
                                            </Link>
                                        </div>

                                        <!-- Tenant (company owner/manager) menu -->
                                        <div v-if="isTenant" class="py-1">
                                            <Link href="/cabinet" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <LayoutDashboard class="w-4 h-4 text-on-surface-muted" /> Панель управления
                                            </Link>
                                            <div class="px-4 pt-2 pb-1">
                                                <p class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide truncate">
                                                    {{ company.name }}
                                                </p>
                                            </div>
                                            <Link href="/cabinet/profile" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <Settings class="w-4 h-4 text-on-surface-muted" /> Профиль компании
                                            </Link>
                                            <Link href="/cabinet/employees" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <User class="w-4 h-4 text-on-surface-muted" /> Сотрудники
                                            </Link>
                                        </div>

                                        <!-- Buyer menu -->
                                        <div v-else class="py-1">
                                            <Link href="/buyer/dashboard" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <LayoutDashboard class="w-4 h-4 text-on-surface-muted" /> Мой кабинет
                                            </Link>
                                            <Link href="/buyer/orders" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <Package class="w-4 h-4 text-on-surface-muted" /> Мои заказы
                                            </Link>
                                            <Link href="/buyer/favorites" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <Heart class="w-4 h-4 text-on-surface-muted" /> Избранное
                                            </Link>
                                            <Link href="/buyer/appointments" @click="userMenuOpen = false"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface hover:bg-surface-muted transition-colors">
                                                <CalendarCheck class="w-4 h-4 text-on-surface-muted" /> Записи на сервис
                                            </Link>
                                        </div>

                                        <div class="border-t border-outline py-1">
                                            <button
                                                @click="logout"
                                                class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors"
                                            >
                                                <LogOut class="w-4 h-4" /> Выйти
                                            </button>
                                        </div>
                                    </div>
                                </Transition>
                            </div>

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
                    <div class="pt-3 border-t border-outline mt-2">
                        <template v-if="user">
                            <p class="text-xs text-on-surface-muted px-2 mb-2 font-semibold uppercase tracking-wide">{{ user.name }}</p>

                            <!-- Admin mobile link -->
                            <Link v-if="isAdmin" href="/admin" @click="mobileMenuOpen = false"
                                class="flex items-center gap-2 nav-link text-sm font-semibold text-primary">
                                <Shield class="w-4 h-4" /> Панель администратора
                            </Link>

                            <!-- Tenant mobile links -->
                            <template v-if="isTenant">
                                <Link href="/cabinet" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2 nav-link text-sm">
                                    <LayoutDashboard class="w-4 h-4" /> Панель управления
                                </Link>
                                <Link href="/cabinet/profile" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2 nav-link text-sm">
                                    <Settings class="w-4 h-4" /> Профиль компании
                                </Link>
                                <Link href="/cabinet/employees" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2 nav-link text-sm">
                                    <User class="w-4 h-4" /> Сотрудники
                                </Link>
                            </template>

                            <!-- Buyer mobile links -->
                            <template v-else>
                                <Link href="/buyer/dashboard" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2 nav-link text-sm">
                                    <LayoutDashboard class="w-4 h-4" /> Мой кабинет
                                </Link>
                                <Link href="/buyer/orders" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2 nav-link text-sm">
                                    <Package class="w-4 h-4" /> Заказы
                                </Link>
                                <Link href="/buyer/favorites" @click="mobileMenuOpen = false"
                                    class="flex items-center gap-2 nav-link text-sm">
                                    <Heart class="w-4 h-4" /> Избранное
                                </Link>
                            </template>

                            <button @click="logout; mobileMenuOpen = false"
                                class="w-full flex items-center gap-2 nav-link text-sm text-danger mt-1">
                                <LogOut class="w-4 h-4" /> Выйти
                            </button>
                        </template>
                        <template v-else>
                            <div class="flex gap-2">
                                <Link href="/login" class="btn-secondary text-sm flex-1 text-center">Войти</Link>
                                <Link href="/register" class="btn-primary text-sm flex-1 text-center">Регистрация</Link>
                            </div>
                        </template>
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
                            <span class="font-bold">Город машин</span>
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
                            <li><Link href="/blog" class="hover:text-white transition-colors">Блог</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-xs uppercase tracking-wider text-gray-400 mb-3">Бизнес</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li><Link href="/register/company" class="hover:text-white transition-colors">Стать резидентом</Link></li>
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
                    <span>&copy; {{ new Date().getFullYear() }} Город машин. Все права защищены.</span>
                    <span>Цифровой двойник авторынка города</span>
                </div>
            </div>
        </footer>
    </div>
</template>
