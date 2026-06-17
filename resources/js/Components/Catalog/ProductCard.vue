<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    product: { type: Object, required: true },
})

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price / 100) + ' ₽'
</script>

<template>
    <Link :href="`/parts/${product.slug}`" class="block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <div class="aspect-square bg-gray-100">
            <img
                v-if="product.cover_photo_url"
                :src="product.cover_photo_url"
                :alt="product.name"
                class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                Нет фото
            </div>
        </div>
        <div class="p-4">
            <h3 class="font-semibold text-gray-900 text-sm truncate">{{ product.name }}</h3>
            <p v-if="product.article_number" class="text-xs text-gray-500 mt-1">
                Арт.: {{ product.article_number }}
            </p>
            <p v-if="product.oem_number" class="text-xs text-gray-500">
                OEM: {{ product.oem_number }}
            </p>
            <div class="flex items-center justify-between mt-2">
                <span class="text-lg font-bold text-blue-600">
                    {{ product.price_retail ? formatPrice(product.price_retail) : '—' }}
                </span>
                <span
                    class="text-xs px-2 py-0.5 rounded-full"
                    :class="product.quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                >
                    {{ product.quantity > 0 ? 'В наличии' : 'Нет в наличии' }}
                </span>
            </div>
        </div>
    </Link>
</template>
