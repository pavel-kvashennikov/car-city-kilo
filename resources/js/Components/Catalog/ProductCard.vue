<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    product: { type: Object, required: true },
})

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price / 100) + ' ₽'
</script>

<template>
    <Link
        :href="`/catalog/parts/${product.slug}`"
        class="card card-hover overflow-hidden group block"
    >
        <div class="aspect-square bg-surface-muted relative overflow-hidden">
            <img
                v-if="product.cover_photo_url"
                :src="product.cover_photo_url"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-secondary text-sm">
                Нет фото
            </div>
        </div>
        <div class="p-5">
            <h3 class="font-semibold text-on-surface text-sm line-clamp-2 group-hover:text-primary transition-colors">
                {{ product.name }}
            </h3>
            <p v-if="product.oem_number" class="text-xs text-on-surface-muted mt-2 font-mono">
                OEM: {{ product.oem_number }}
            </p>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-outline/30">
                <span class="text-lg font-bold text-gradient">
                    {{ product.price_retail ? formatPrice(product.price_retail) : '—' }}
                </span>
                <span
                    class="badge"
                    :class="(product.stock_quantity ?? product.quantity) > 0 ? 'badge-success' : 'badge-danger'"
                >
                    {{ (product.stock_quantity ?? product.quantity) > 0 ? 'В наличии' : 'Нет' }}
                </span>
            </div>
        </div>
    </Link>
</template>
