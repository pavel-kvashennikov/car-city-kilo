<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    serviceItems: { type: Array, default: () => [] },
})

const selected = ref([])

const toggle = (item) => {
    const idx = selected.value.findIndex(s => s.id === item.id)
    if (idx >= 0) {
        selected.value.splice(idx, 1)
    } else {
        selected.value.push(item)
    }
}

const isSelected = (item) => selected.value.some(s => s.id === item.id)

const total = computed(() => selected.value.reduce((sum, item) => sum + item.price, 0))
const totalDuration = computed(() => selected.value.reduce((sum, item) => sum + (item.duration_minutes || 0), 0))

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽'
const formatDuration = (minutes) => {
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    return h > 0 ? `${h}ч ${m}мин` : `${m}мин`
}
</script>

<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">Калькулятор ремонта</h3>

        <div class="space-y-2">
            <button
                v-for="item in serviceItems"
                :key="item.id"
                class="w-full flex items-center justify-between p-3 rounded-lg border transition text-left"
                :class="isSelected(item) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'"
                @click="toggle(item)"
            >
                <div>
                    <span class="font-medium text-sm">{{ item.name }}</span>
                    <span v-if="item.duration_minutes" class="text-xs text-gray-500 ml-2">
                        ~{{ formatDuration(item.duration_minutes) }}
                    </span>
                </div>
                <span class="font-semibold text-sm">{{ formatPrice(item.price) }}</span>
            </button>
        </div>

        <div v-if="selected.length > 0" class="bg-blue-50 p-4 rounded-lg">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Услуг: {{ selected.length }}</span>
                <span>~{{ formatDuration(totalDuration) }}</span>
            </div>
            <div class="flex justify-between mt-2">
                <span class="font-semibold">Итого:</span>
                <span class="text-xl font-bold text-blue-600">{{ formatPrice(total) }}</span>
            </div>
        </div>
    </div>
</template>
