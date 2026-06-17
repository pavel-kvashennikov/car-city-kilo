<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    contact_name: '',
    contact_phone: '',
    contact_email: '',
    delivery_address: '',
    notes: '',
})

const submit = () => {
    form.post('/orders')
}
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <h2 class="text-xl font-semibold">Оформление заказа</h2>

        <div class="space-y-4">
            <h3 class="font-medium text-gray-700">Контактные данные</h3>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Имя *</label>
                <input v-model="form.contact_name" type="text" required class="w-full rounded-lg border-gray-300" />
                <p v-if="form.errors.contact_name" class="text-sm text-red-600 mt-1">{{ form.errors.contact_name }}</p>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Телефон *</label>
                <input v-model="form.contact_phone" type="tel" required class="w-full rounded-lg border-gray-300" />
                <p v-if="form.errors.contact_phone" class="text-sm text-red-600 mt-1">{{ form.errors.contact_phone }}</p>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Email</label>
                <input v-model="form.contact_email" type="email" class="w-full rounded-lg border-gray-300" />
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="font-medium text-gray-700">Доставка</h3>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Адрес доставки</label>
                <textarea v-model="form.delivery_address" rows="2" class="w-full rounded-lg border-gray-300" />
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Комментарий</label>
            <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300" />
        </div>

        <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50"
        >
            {{ form.processing ? 'Оформление...' : 'Подтвердить заказ' }}
        </button>
    </form>
</template>
