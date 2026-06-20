<script setup>
defineProps({
    modelValue: { type: Boolean, default: false },
    label: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none" :class="{ 'opacity-50 cursor-not-allowed': disabled }">
        <input
            type="checkbox"
            :checked="modelValue"
            :disabled="disabled"
            class="w-4 h-4 rounded border-outline text-primary focus:ring-2 focus:ring-primary/30 focus:ring-offset-0"
            @change="$emit('update:modelValue', $event.target.checked)"
        />
        <span v-if="label" class="text-sm text-on-surface">{{ label }}</span>
        <slot />
    </label>
</template>
