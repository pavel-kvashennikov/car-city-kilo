<script setup>
import { ref } from 'vue'

const notifications = ref([])

const add = (notification) => {
    const id = Date.now()
    notifications.value.push({ ...notification, id })
    setTimeout(() => remove(id), notification.duration || 5000)
}

const remove = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
}

defineExpose({ add })

const typeClasses = {
    success: 'bg-green-50 border-green-200 text-green-800',
    error: 'bg-red-50 border-red-200 text-red-800',
    warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
    info: 'bg-blue-50 border-blue-200 text-blue-800',
}
</script>

<template>
    <div class="fixed top-4 right-4 z-[100] space-y-2 w-80">
        <transition-group
            enter-active-class="transition transform duration-300"
            enter-from-class="opacity-0 translate-x-full"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition transform duration-200"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 translate-x-full"
        >
            <div
                v-for="n in notifications"
                :key="n.id"
                class="rounded-lg border p-4 shadow-lg"
                :class="typeClasses[n.type] || typeClasses.info"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p v-if="n.title" class="font-medium text-sm">{{ n.title }}</p>
                        <p class="text-sm mt-0.5">{{ n.message }}</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 ml-2" @click="remove(n.id)">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition-group>
    </div>
</template>
