<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const form = useForm({
    category_id: props.filters.category_id || '',
    brand: props.filters.brand || '',
    price_from: props.filters.price_from || '',
    price_to: props.filters.price_to || '',
    in_stock: props.filters.in_stock || '',
})

const submit = () => {
    form.get('/parts', { preserveState: true })
}

const reset = () => {
    form.reset()
    form.get('/parts')
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
            <select v-model="form.category_id" class="w-full rounded-lg border-gray-300" @change="submit">
                <option value="">Все категории</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Бренд</label>
            <input v-model="form.brand" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Бренд" />
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Цена от</label>
                <input v-model="form.price_from" type="number" class="w-full rounded-lg border-gray-300 text-sm" />
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">до</label>
                <input v-model="form.price_to" type="number" class="w-full rounded-lg border-gray-300 text-sm" />
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.in_stock" type="checkbox" class="rounded border-gray-300" true-value="1" false-value="" @change="submit" />
                Только в наличии
            </label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700">
                Применить
            </button>
            <button type="button" class="px-4 py-2 rounded-lg text-sm border border-gray-300 hover:bg-gray-50" @click="reset">
                Сброс
            </button>
        </div>
    </form>
</template>
