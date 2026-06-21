<script setup>
import { useForm } from '@inertiajs/vue3';
import PhoneInput from '@/Components/UI/PhoneInput.vue';

const props = defineProps({
    vehicleId: { type: Number, required: true },
})

const form = useForm({
    vehicle_id: props.vehicleId,
    type: 'test_drive',
    name: '',
    phone: '',
    email: '',
    preferred_date: '',
    comment: '',
})

const submit = () => {
    form.post('/leads')
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <h3 class="text-lg font-semibold">Записаться на тест-драйв</h3>
        <div>
            <input v-model="form.name" type="text" placeholder="Ваше имя *" required class="w-full rounded-lg border-gray-300" />
            <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>
        <div>
            <PhoneInput v-model="form.phone" required input-class="w-full rounded-lg border-gray-300" />
            <p v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</p>
        </div>
        <div>
            <input v-model="form.email" type="email" placeholder="Email" class="w-full rounded-lg border-gray-300" />
        </div>
        <div>
            <input v-model="form.preferred_date" type="date" class="w-full rounded-lg border-gray-300" />
        </div>
        <div>
            <textarea v-model="form.comment" placeholder="Комментарий" rows="3" class="w-full rounded-lg border-gray-300" />
        </div>
        <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50"
        >
            {{ form.processing ? 'Отправка...' : 'Записаться' }}
        </button>
    </form>
</template>
