<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

defineProps({
    brands: { type: Array, default: () => [] },
    models: { type: Array, default: () => [] },
})

const form = useForm({
    brand_id: '',
    model_id: '',
    year: '',
    vin: '',
    mileage: '',
    price: '',
    color: '',
    engine_type: '',
    engine_volume: '',
    engine_power: '',
    transmission: '',
    drive_type: '',
    body_type: '',
    condition: 'used',
    description: '',
})

const submit = () => {
    form.post('/cabinet/dealer/vehicles')
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Добавить автомобиль</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Марка *</label>
                    <select v-model="form.brand_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Выберите</option>
                        <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Модель *</label>
                    <select v-model="form.model_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Выберите</option>
                        <option v-for="m in models" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <Input v-model="form.year" label="Год *" type="number" required />
                <Input v-model="form.mileage" label="Пробег *" type="number" required />
                <Input v-model="form.price" label="Цена *" type="number" required />
            </div>
            <Input v-model="form.vin" label="VIN" />
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.color" label="Цвет" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">КПП</label>
                    <select v-model="form.transmission" class="w-full rounded-lg border-gray-300">
                        <option value="">—</option>
                        <option value="manual">Механика</option>
                        <option value="automatic">Автомат</option>
                        <option value="robot">Робот</option>
                        <option value="cvt">Вариатор</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-gray-300" />
            </div>
            <Button type="submit" :loading="form.processing">Создать</Button>
        </form>
    </CabinetLayout>
</template>
