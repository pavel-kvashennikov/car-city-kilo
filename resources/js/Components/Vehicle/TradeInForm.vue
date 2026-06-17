<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    vehicleId: { type: Number, required: true },
})

const form = useForm({
    vehicle_id: props.vehicleId,
    type: 'trade_in',
    name: '',
    phone: '',
    meta: {
        trade_in_brand: '',
        trade_in_model: '',
        trade_in_year: '',
        trade_in_mileage: '',
        trade_in_condition: 'good',
    },
})

const submit = () => {
    form.post('/leads')
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <h3 class="text-lg font-semibold">Trade-in оценка</h3>
        <p class="text-sm text-gray-500">Узнайте стоимость вашего авто в зачёт</p>
        <div>
            <input v-model="form.meta.trade_in_brand" type="text" placeholder="Марка вашего авто *" required class="w-full rounded-lg border-gray-300" />
        </div>
        <div>
            <input v-model="form.meta.trade_in_model" type="text" placeholder="Модель *" required class="w-full rounded-lg border-gray-300" />
        </div>
        <div class="grid grid-cols-2 gap-3">
            <input v-model="form.meta.trade_in_year" type="number" placeholder="Год" class="rounded-lg border-gray-300" />
            <input v-model="form.meta.trade_in_mileage" type="number" placeholder="Пробег" class="rounded-lg border-gray-300" />
        </div>
        <div>
            <select v-model="form.meta.trade_in_condition" class="w-full rounded-lg border-gray-300">
                <option value="excellent">Отличное</option>
                <option value="good">Хорошее</option>
                <option value="fair">Нормальное</option>
                <option value="poor">Плохое</option>
            </select>
        </div>
        <div>
            <input v-model="form.name" type="text" placeholder="Ваше имя *" required class="w-full rounded-lg border-gray-300" />
        </div>
        <div>
            <input v-model="form.phone" type="tel" placeholder="Телефон *" required class="w-full rounded-lg border-gray-300" />
        </div>
        <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 disabled:opacity-50"
        >
            {{ form.processing ? 'Отправка...' : 'Оценить' }}
        </button>
    </form>
</template>
