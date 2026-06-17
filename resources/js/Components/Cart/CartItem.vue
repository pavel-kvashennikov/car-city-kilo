<script setup>
import { useCartStore } from '@/Stores/cart'

defineProps({
    item: { type: Object, required: true },
})

const cart = useCartStore()

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg">
        <div class="w-16 h-16 bg-gray-200 rounded-lg shrink-0 overflow-hidden">
            <img v-if="item.snapshot?.image" :src="item.snapshot.image" class="w-full h-full object-cover" />
        </div>
        <div class="min-w-0 flex-1">
            <h4 class="text-sm font-medium text-gray-900 truncate">{{ item.snapshot?.name || 'Товар' }}</h4>
            <p class="text-sm text-blue-600 font-medium mt-1">{{ formatPrice(item.unit_price) }}</p>
            <div class="flex items-center gap-2 mt-1">
                <button
                    class="w-7 h-7 rounded border border-gray-300 text-sm hover:bg-gray-100"
                    @click="cart.updateQuantity(item.id, item.quantity - 1)"
                >
                    −
                </button>
                <span class="text-sm w-6 text-center">{{ item.quantity }}</span>
                <button
                    class="w-7 h-7 rounded border border-gray-300 text-sm hover:bg-gray-100"
                    @click="cart.updateQuantity(item.id, item.quantity + 1)"
                >
                    +
                </button>
                <button class="ml-auto text-red-400 hover:text-red-600 text-sm" @click="cart.removeItem(item.id)">
                    Удалить
                </button>
            </div>
        </div>
    </div>
</template>
