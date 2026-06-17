<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    serviceProfileId: { type: Number, required: true },
    masters: { type: Array, default: () => [] },
})

const emit = defineEmits(['select'])

const selectedDate = ref('')
const selectedMasterId = ref(null)
const slots = ref([])
const loading = ref(false)

const today = computed(() => new Date().toISOString().split('T')[0])

const fetchSlots = async () => {
    if (!selectedDate.value) return
    loading.value = true
    try {
        const params = {
            service_profile_id: props.serviceProfileId,
            date: selectedDate.value,
        }
        if (selectedMasterId.value) params.master_id = selectedMasterId.value
        const { data } = await axios.get('/api/slots', { params })
        slots.value = data.data ?? data
    } finally {
        loading.value = false
    }
}

watch([selectedDate, selectedMasterId], fetchSlots)

const selectSlot = (slot) => {
    emit('select', slot)
}
</script>

<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">Выберите дату и время</h3>

        <div v-if="masters.length > 0">
            <label class="block text-sm text-gray-600 mb-1">Мастер</label>
            <select v-model="selectedMasterId" class="w-full rounded-lg border-gray-300">
                <option :value="null">Любой мастер</option>
                <option v-for="master in masters" :key="master.id" :value="master.id">{{ master.name }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Дата</label>
            <input v-model="selectedDate" type="date" :min="today" class="w-full rounded-lg border-gray-300" />
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-4">Загрузка слотов...</div>

        <div v-else-if="slots.length > 0" class="grid grid-cols-3 gap-2">
            <button
                v-for="slot in slots"
                :key="slot.id"
                class="py-2 px-3 rounded-lg border text-sm text-center hover:bg-blue-50 hover:border-blue-300 transition"
                @click="selectSlot(slot)"
            >
                {{ slot.start_time }} — {{ slot.end_time }}
            </button>
        </div>

        <p v-else-if="selectedDate" class="text-sm text-gray-500 text-center py-4">
            Нет доступных слотов на выбранную дату
        </p>
    </div>
</template>
