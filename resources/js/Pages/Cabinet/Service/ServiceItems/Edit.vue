<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
    item: { type: Object, required: true },
})

const form = useForm({
    name: props.item.name,
    description: props.item.description ?? '',
    price_fixed: props.item.price_fixed ?? '',
    price_from: props.item.price_from ?? '',
    price_to: props.item.price_to ?? '',
    duration_minutes: props.item.duration_minutes ?? 60,
})

const submit = () => {
    form.put(`/cabinet/service/items/${props.item.id}`)
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Редактирование услуги</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Название *" required />
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
            <Button type="submit" :loading="form.processing">Сохранить</Button>
        </form>
    </CabinetLayout>
</template>
