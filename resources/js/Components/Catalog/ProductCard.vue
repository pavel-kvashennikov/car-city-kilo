<script setup>
import { Link } from '@inertiajs/vue3';
import { Settings } from 'lucide-vue-next';

defineProps({ product: { type: Object, required: true } });

const fmt = (n) => new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
const imgSrc = (url) => {
    if (!url) return null;
    return url.startsWith('http') ? url : `/storage/${url}`;
};
</script>

<template>
    <Link :href="`/catalog/parts/${product.slug}`" class="card card-hover overflow-hidden group block">
        <div class="aspect-square bg-surface-muted relative overflow-hidden">
            <img
                v-if="product.cover_photo_url || product.attributes?.images?.[0]"
                :src="imgSrc(product.cover_photo_url || product.attributes?.images?.[0])"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-surface-dim">
                <Settings class="w-10 h-10 text-outline" />
            </div>
            <div class="absolute top-2 right-2">
                <span :class="['badge', (product.stock_quantity ?? 0) > 0 ? 'badge-success' : 'badge-neutral']">
                    {{ (product.stock_quantity ?? 0) > 0 ? 'В наличии' : 'Под заказ' }}
                </span>
            </div>
        </div>
        <div class="p-3">
            <h3 class="font-semibold text-xs text-on-surface line-clamp-2 group-hover:text-primary transition-colors leading-relaxed min-h-[2.4rem]">
                {{ product.name }}
            </h3>
            <p v-if="product.oem_number" class="text-xs text-on-surface-muted mt-1 font-mono truncate">
                {{ product.oem_number }}
            </p>
            <p class="text-sm font-extrabold text-gradient mt-2">
                {{ product.price_retail ? fmt(product.price_retail) : '—' }}
            </p>
        </div>
    </Link>
</template>
