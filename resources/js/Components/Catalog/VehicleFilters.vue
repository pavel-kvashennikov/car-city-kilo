<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    brands: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const form = useForm({
    brand_id: props.filters.brand_id || '',
    price_from: props.filters.price_from || '',
    price_to: props.filters.price_to || '',
    year_from: props.filters.year_from || '',
    year_to: props.filters.year_to || '',
    transmission: props.filters.transmission || '',
    engine_type: props.filters.engine_type || '',
})

const submit = () => {
    form.get('/cars', { preserveState: true })
}

const reset = () => {
    form.reset()
    form.get('/cars')
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Марка</label>
            <select v-model="form.brand_id" class="w-full rounded-lg border-gray-300" @change="submit">
                <option value="">Все марки</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Цена от</label>
                <input v-model="form.price_from" type="number" class="w-full rounded-lg border-gray-300 text-sm" placeholder="от" />
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">до</label>
                <input v-model="form.price_to" type="number" class="w-full rounded-lg border-gray-300 text-sm" placeholder="до" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Год от</label>
                <input v-model="form.year_from" type="number" class="w-full rounded-lg border-gray-300 text-sm" placeholder="от" />
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">до</label>
                <input v-model="form.year_to" type="number" class="w-full rounded-lg border-gray-300 text-sm" placeholder="до" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">КПП</label>
            <select v-model="form.transmission" class="w-full rounded-lg border-gray-300" @change="submit">
                <option value="">Любая</option>
                <option value="manual">Механика</option>
                <option value="automatic">Автомат</option>
                <option value="robot">Робот</option>
                <option value="cvt">Вариатор</option>
            </select>
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
