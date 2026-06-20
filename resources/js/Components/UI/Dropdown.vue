<script setup>
import { ref } from 'vue';

defineProps({
    align: { type: String, default: 'left' },
});

const open = ref(false);
const close = () => { open.value = false; };
</script>

<template>
    <div class="relative" @mouseleave="close">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                :class="[
                    'absolute z-50 mt-2 w-52 rounded-xl bg-surface-elevated shadow-elevated border border-outline overflow-hidden origin-top',
                    align === 'right' ? 'right-0' : 'left-0',
                ]"
                @click="close"
            >
                <div class="py-1">
                    <slot />
                </div>
            </div>
        </Transition>
    </div>
</template>
