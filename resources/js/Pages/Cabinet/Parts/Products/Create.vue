<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

defineProps({
    categories: { type: Array, default: () => [] },
})

const form = useForm({
    name: '',
    article_number: '',
    oem_number: '',
    brand: '',
    category_id: '',
    price_retail: '',
    price_wholesale: '',
    quantity: '',
    condition: 'new',
    description: '',
})

const submit = () => {
    form.post('/cabinet/parts/products')
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Добавить товар</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Название *" :error="form.errors.name" required />
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.article_number" label="Артикул" :error="form.errors.article_number" />
                <Input v-model="form.oem_number" label="OEM номер" :error="form.errors.oem_number" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.brand" label="Бренд" :error="form.errors.brand" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                    <select v-model="form.category_id" class="w-full rounded-lg border-gray-300">
                        <option value="">—</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <Input v-model="form.price_retail" label="Цена (розн.)" type="number" />
                <Input v-model="form.price_wholesale" label="Цена (опт.)" type="number" />
                <Input v-model="form.quantity" label="Кол-во" type="number" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300" />
            </div>
            <Button type="submit" :loading="form.processing">Создать</Button>
        </form>
    </CabinetLayout>
</template>
