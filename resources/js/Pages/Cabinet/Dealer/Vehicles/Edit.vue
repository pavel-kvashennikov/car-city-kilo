<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
    vehicle: { type: Object, required: true },
    brands: { type: Array, default: () => [] },
})

const form = useForm({
    brand_id: props.vehicle.brand_id,
    model_id: props.vehicle.model_id,
    year: props.vehicle.year,
    vin: props.vehicle.vin ?? '',
    mileage: props.vehicle.mileage,
    price: props.vehicle.price,
    color: props.vehicle.color ?? '',
    transmission: props.vehicle.transmission ?? '',
    condition: props.vehicle.condition ?? 'used',
    description: props.vehicle.description ?? '',
    status: props.vehicle.status ?? 'draft',
})

const submit = () => {
    form.put(`/cabinet/dealer/vehicles/${props.vehicle.id}`)
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Редактирование автомобиля</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Марка</label>
                    <select v-model="form.brand_id" class="w-full rounded-lg border-gray-300">
                        <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                </div>
                <Input v-model="form.year" label="Год" type="number" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.mileage" label="Пробег" type="number" />
                <Input v-model="form.price" label="Цена" type="number" />
            </div>
            <Input v-model="form.vin" label="VIN" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                <select v-model="form.status" class="w-full rounded-lg border-gray-300">
                    <option value="draft">Черновик</option>
                    <option value="pending">На модерации</option>
                    <option value="active">Активно</option>
                    <option value="sold">Продано</option>
                    <option value="archived">Архив</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-gray-300" />
            </div>
            <Button type="submit" :loading="form.processing">Сохранить</Button>
        </form>
    </CabinetLayout>
</template>
