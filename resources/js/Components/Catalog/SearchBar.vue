<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Search } from 'lucide-vue-next'

const props = defineProps({
    placeholder: { type: String, default: 'Поиск...' },
    action: { type: String, default: '/parts/search' },
    value: { type: String, default: '' },
})

const query = ref(props.value)

const submit = () => {
    if (query.value.trim()) {
        router.get(props.action, { q: query.value })
    }
}
</script>

<template>
    <form class="relative" @submit.prevent="submit">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
        <input
            v-model="query"
            type="text"
            :placeholder="placeholder"
            class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
        />
        <button
            type="submit"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700"
        >
            Найти
        </button>
    </form>
</template>
