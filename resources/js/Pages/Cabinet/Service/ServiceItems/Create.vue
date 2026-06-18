<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const form = useForm({
    name: '',
    description: '',
    price_fixed: '',
    price_from: '',
    price_to: '',
    duration_minutes: 60,
})

const submit = () => {
    form.post('/cabinet/service/items')
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Добавить услугу</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Название *" :error="form.errors.name" required />
            <div class="grid grid-cols-3 gap-4">
                <Input v-model="form.price_fixed" label="Фикс. цена" type="number" />
                <Input v-model="form.price_from" label="Цена от" type="number" />
                <Input v-model="form.price_to" label="Цена до" type="number" />
            </div>
            <Input v-model="form.duration_minutes" label="Длительность (мин)" type="number" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300" />
            </div>
            <Button type="submit" :loading="form.processing">Создать</Button>
        </form>
    </CabinetLayout>
</template>
