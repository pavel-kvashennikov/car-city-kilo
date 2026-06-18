<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import Button from '@/Components/UI/Button.vue'
import Input from '@/Components/UI/Input.vue'

const props = defineProps({
    cart: { type: Object, default: () => null },
})

const checkoutForm = useForm({
    client_name: '',
    client_phone: '',
    client_email: '',
    delivery_method: 'pickup',
    delivery_address: null,
    comment: '',
})

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'

const total = () => {
    if (!props.cart?.items) return 0
    return props.cart.items.reduce((sum, i) => sum + i.price * i.quantity, 0)
}

const removeItem = (id) => {
    router.delete(`/cart/items/${id}`)
}

const checkout = () => {
    checkoutForm.post('/cart/checkout')
}
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Корзина</h1>

            <div v-if="!cart?.items?.length" class="text-center text-gray-500 py-12">
                Корзина пуста
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <div
                        v-for="item in cart.items"
                        :key="item.id"
                        class="bg-white rounded-xl shadow p-4 flex justify-between items-center"
                    >
                        <div>
                            <p class="font-medium">{{ item.itemable?.name ?? 'Товар' }}</p>
                            <p class="text-sm text-gray-500">{{ item.quantity }} шт. × {{ formatPrice(item.price) }}</p>
                        </div>
                        <button class="text-red-600 text-sm hover:underline" @click="removeItem(item.id)">
                            Удалить
                        </button>
                    </div>
                    <p class="text-right text-lg font-bold">Итого: {{ formatPrice(total()) }}</p>
                </div>

                <form class="bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="checkout">
                    <h2 class="font-semibold text-lg">Оформление заказа</h2>
                    <Input v-model="checkoutForm.client_name" label="Имя *" :error="checkoutForm.errors.client_name" required />
                    <Input v-model="checkoutForm.client_phone" label="Телефон *" :error="checkoutForm.errors.client_phone" required />
                    <Input v-model="checkoutForm.client_email" label="Email" type="email" :error="checkoutForm.errors.client_email" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Способ получения</label>
                        <select v-model="checkoutForm.delivery_method" class="w-full rounded-lg border-gray-300">
                            <option value="pickup">Самовывоз</option>
                            <option value="delivery">Доставка</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Комментарий</label>
                        <textarea v-model="checkoutForm.comment" rows="2" class="w-full rounded-lg border-gray-300" />
                    </div>
                    <Button type="submit" :loading="checkoutForm.processing" class="w-full">
                        Оформить заказ
                    </Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
