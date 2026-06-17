<script setup>
defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'md' },
})

defineEmits(['close'])

const maxWidthClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
}
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/50" @click="$emit('close')" />
            <div :class="['relative bg-white rounded-xl shadow-xl w-full mx-4', maxWidthClasses[maxWidth]]">
                <div v-if="title" class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">{{ title }}</h3>
                    <button class="text-gray-400 hover:text-gray-600" @click="$emit('close')">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <slot />
                </div>
                <div v-if="$slots.footer" class="p-4 border-t bg-gray-50 rounded-b-xl">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
