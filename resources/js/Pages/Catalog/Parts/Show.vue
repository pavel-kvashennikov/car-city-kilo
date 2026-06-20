<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronLeft, ShoppingCart, Package } from 'lucide-vue-next';
import { mediaUrl } from '@/lib/mediaUrl';

const props = defineProps({ product: Object });
const qty = ref(1);
const addedToCart = ref(false);
const activeImg = ref(0);

const fmt = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' ₽';

const imgSrc = mediaUrl;

const images = props.product?.attributes?.images ?? [];

const cartForm = useForm({ itemable_type: 'App\\Models\\ProductProxy', itemable_id: props.product?.id, quantity: 1 });
function addToCart() {
    addedToCart.value = true;
    setTimeout(() => { addedToCart.value = false; }, 2500);
}
</script>

<template>
    <AppLayout>
        <div class="container-app py-6">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-on-surface-muted mb-5">
                <Link href="/catalog/parts" class="hover:text-primary flex items-center gap-1">
                    <ChevronLeft class="w-3.5 h-3.5" /> Запчасти
                </Link>
                <span>/</span>
                <span class="text-on-surface truncate">{{ product.name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Gallery -->
                <div class="lg:col-span-2 space-y-5">
                    <div class="card overflow-hidden">
                        <div class="aspect-square bg-surface-muted relative overflow-hidden max-h-96">
                            <img
                                v-if="images[activeImg]"
                                :src="imgSrc(images[activeImg])"
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <Package class="w-16 h-16 text-outline" />
                            </div>
                        </div>
                        <div v-if="images.length > 1" class="flex gap-2 p-3 overflow-x-auto bg-surface-muted">
                            <button
                                v-for="(img, i) in images"
                                :key="i"
                                @click="activeImg = i"
                                class="shrink-0 w-16 h-14 rounded-lg overflow-hidden border-2 transition-colors"
                                :class="activeImg === i ? 'border-primary-bright' : 'border-transparent hover:border-outline'"
                            >
                                <img :src="imgSrc(img)" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="card p-5">
                        <h2 class="font-bold text-sm text-on-surface mb-3">Характеристики</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-0">
                            <template v-for="(row, i) in [
                                { label: 'Бренд', value: product.brand },
                                { label: 'Артикул', value: product.article_number },
                                { label: 'OEM-номер', value: product.oem_number },
                                { label: 'Штрихкод', value: product.barcode },
                                { label: 'Состояние', value: product.condition === 'new' ? 'Новая' : 'Б/У' },
                                { label: 'Тип', value: product.part_type === 'original' ? 'Оригинал' : 'Аналог' },
                                { label: 'Гарантия', value: product.attributes?.warranty },
                                { label: 'Страна', value: product.attributes?.country },
                            ]" :key="i">
                                <div v-if="row.value" class="flex justify-between py-2 border-b border-outline text-sm">
                                    <dt class="text-on-surface-muted">{{ row.label }}</dt>
                                    <dd class="font-medium text-on-surface font-mono">{{ row.value }}</dd>
                                </div>
                            </template>
                        </dl>
                    </div>

                    <!-- Cross numbers -->
                    <div v-if="product.cross_numbers?.length" class="card p-5">
                        <h2 class="font-bold text-sm text-on-surface mb-3">Кросс-номера (аналоги)</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="cn in product.cross_numbers"
                                :key="cn.id"
                                class="badge badge-neutral font-mono"
                            >
                                <span v-if="cn.brand" class="text-primary">{{ cn.brand }}: </span>{{ cn.number }}
                            </span>
                        </div>
                    </div>

                    <!-- Applicability -->
                    <div v-if="product.applicabilities?.length" class="card p-5">
                        <h2 class="font-bold text-sm text-on-surface mb-3">Применимость</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-outline">
                                        <th class="py-2 pr-4 font-semibold text-on-surface-muted text-xs uppercase tracking-wide">Марка</th>
                                        <th class="py-2 pr-4 font-semibold text-on-surface-muted text-xs uppercase tracking-wide">Модель</th>
                                        <th class="py-2 font-semibold text-on-surface-muted text-xs uppercase tracking-wide">Годы</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="app in product.applicabilities" :key="app.id" class="border-b border-outline/50">
                                        <td class="py-2 pr-4">{{ app.brand?.name || '—' }}</td>
                                        <td class="py-2 pr-4">{{ app.car_model?.name || '—' }}</td>
                                        <td class="py-2 text-on-surface-muted">{{ app.year_from || '...' }} – {{ app.year_to || '...' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="product.description" class="card p-5">
                        <h2 class="font-bold text-sm text-on-surface mb-2">Описание</h2>
                        <p class="text-sm text-on-surface-muted leading-relaxed">{{ product.description }}</p>
                    </div>
                </div>

                <!-- Sidebar: buy -->
                <div>
                    <div class="card p-5 sticky top-20 space-y-4">
                        <div>
                            <p v-if="product.category?.name" class="text-xs text-on-surface-muted mb-1">{{ product.category.name }}</p>
                            <h1 class="font-extrabold text-base text-on-surface leading-tight">{{ product.name }}</h1>
                        </div>

                        <div>
                            <div class="text-3xl font-extrabold text-gradient">{{ fmt(product.price_retail) }}</div>
                            <div v-if="product.price_wholesale" class="text-xs text-on-surface-muted mt-1">
                                Оптом {{ fmt(product.price_wholesale) }} (от {{ product.wholesale_min_qty }} шт.)
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <span v-if="product.stock_quantity > 0" class="badge badge-success">
                                ✓ В наличии {{ product.stock_quantity }} шт.
                            </span>
                            <span v-else class="badge badge-neutral">Под заказ</span>
                        </div>

                        <!-- Seller -->
                        <div v-if="product.parts_profile?.company" class="pt-3 border-t border-outline">
                            <p class="text-xs text-on-surface-muted mb-1">Продавец</p>
                            <Link :href="`/companies/${product.parts_profile.company.slug}`" class="text-sm font-semibold text-on-surface hover:text-primary transition-colors">
                                {{ product.parts_profile.company.name }}
                            </Link>
                        </div>

                        <!-- Qty + Cart -->
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-outline rounded-lg overflow-hidden">
                                <button @click="qty = Math.max(1, qty - 1)" class="px-3 py-2 text-on-surface-muted hover:bg-surface-muted">−</button>
                                <input v-model.number="qty" type="number" min="1" class="w-12 text-center text-sm font-semibold border-0 outline-none" />
                                <button @click="qty++" class="px-3 py-2 text-on-surface-muted hover:bg-surface-muted">+</button>
                            </div>
                            <button
                                @click="addToCart"
                                class="flex-1 btn-primary !justify-center !text-sm !py-2.5"
                                :class="addedToCart ? '!bg-gradient-to-r !from-tertiary !to-tertiary-bright' : ''"
                            >
                                <ShoppingCart class="w-4 h-4" />
                                {{ addedToCart ? 'Добавлено!' : 'В корзину' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
