<script setup>
import { X } from 'lucide-vue-next';

defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'md' },
});

defineEmits(['close']);

const maxWidthClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-carbon-deep/50 backdrop-blur-sm" @click="$emit('close')" />
                <div :class="['relative bg-surface-elevated rounded-2xl shadow-elevated w-full', maxWidthClasses[maxWidth]]">
                    <div v-if="title" class="flex items-center justify-between px-5 py-4 border-b border-outline">
                        <h3 class="font-bold text-on-surface">{{ title }}</h3>
                        <button class="btn-ghost !p-1.5" @click="$emit('close')">
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="p-5">
                        <slot />
                    </div>
                    <div v-if="$slots.footer" class="px-5 py-4 border-t border-outline bg-surface-muted rounded-b-2xl flex items-center justify-end gap-2">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
