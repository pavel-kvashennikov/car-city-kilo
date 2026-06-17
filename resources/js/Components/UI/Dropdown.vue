<script setup>
import { ref } from 'vue'

defineProps({
    align: { type: String, default: 'left' },
})

const open = ref(false)

const close = () => { open.value = false }
</script>

<template>
    <div class="relative" @mouseleave="close">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>
        <div
            v-show="open"
            :class="[
                'absolute z-50 mt-2 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black/5',
                align === 'right' ? 'right-0' : 'left-0',
            ]"
            @click="close"
        >
            <div class="py-1">
                <slot />
            </div>
        </div>
    </div>
</template>
