<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
})

const form = useForm({
    name: props.product.name,
    category_id: props.product.category_id ?? '',
    article_number: props.product.article_number ?? '',
    oem_number: props.product.oem_number ?? '',
    brand: props.product.brand ?? '',
    price_retail: props.product.price_retail,
    price_wholesale: props.product.price_wholesale ?? '',
    stock_quantity: props.product.stock_quantity,
    status: props.product.status ?? 'active',
    description: props.product.description ?? '',
})

const submit = () => {
    form.put(`/cabinet/parts/products/${props.product.id}`)
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Редактирование товара</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Название *" :error="form.errors.name" required />
            <div class="grid grid-cols-2 gap-4">
                <Input v-model="form.article_number" label="Артикул" />
                <Input v-model="form.oem_number" label="OEM номер" />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <Input v-model="form.price_retail" label="Цена (розн.)" type="number" />
                <Input v-model="form.price_wholesale" label="Цена (опт.)" type="number" />
                <Input v-model="form.stock_quantity" label="Остаток" type="number" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                <select v-model="form.status" class="w-full rounded-lg border-gray-300">
                    <option value="active">Активен</option>
                    <option value="inactive">Неактивен</option>
                    <option value="out_of_stock">Нет в наличии</option>
                    <option value="archived">Архив</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300" />
            </div>
            <Button type="submit" :loading="form.processing">Сохранить</Button>
        </form>
    </CabinetLayout>
</template>
