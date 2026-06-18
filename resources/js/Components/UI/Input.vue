<script setup>
defineProps({
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-semibold text-on-surface mb-1.5">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            class="input-field disabled:bg-surface-muted disabled:cursor-not-allowed"
            :class="{ '!border-danger focus:!shadow-[0_0_0_3px_rgb(186_26_26_/_0.15)]': error }"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1.5 text-sm text-danger">{{ error }}</p>
    </div>
</template>
