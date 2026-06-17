<script setup>
import CartItem from './CartItem.vue'
import { useCartStore } from '@/Stores/cart'
import { Link } from '@inertiajs/vue3'

defineProps({
    open: { type: Boolean, default: false },
})

defineEmits(['close'])

const cart = useCartStore()

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-50 flex">
            <div class="fixed inset-0 bg-black/50" @click="$emit('close')" />
            <div class="ml-auto relative w-full max-w-md bg-white shadow-xl flex flex-col">
                <div class="flex items-center justify-between p-4 border-b">
                    <h2 class="text-lg font-semibold">Корзина ({{ cart.itemCount }})</h2>
                    <button class="text-gray-400 hover:text-gray-600" @click="$emit('close')">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div v-if="cart.items.length === 0" class="flex-1 flex items-center justify-center text-gray-500">
                    Корзина пуста
                </div>

                <div v-else class="flex-1 overflow-y-auto p-4 space-y-3">
                    <CartItem v-for="item in cart.items" :key="item.id" :item="item" />
                </div>

                <div v-if="cart.items.length > 0" class="border-t p-4 space-y-3">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Итого:</span>
                        <span>{{ formatPrice(cart.total) }}</span>
                    </div>
                    <Link
                        href="/checkout"
                        class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-medium hover:bg-blue-700"
                        @click="$emit('close')"
                    >
                        Оформить заказ
                    </Link>
                </div>
            </div>
        </div>
    </Teleport>
</template>
