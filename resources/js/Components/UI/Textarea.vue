<script setup>
defineProps({
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    rows: { type: [String, Number], default: 4 },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    hint: { type: String, default: '' },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <textarea
            :value="modelValue"
            :placeholder="placeholder"
            :rows="rows"
            :required="required"
            :disabled="disabled"
            class="input-field resize-y leading-relaxed disabled:bg-surface-muted disabled:cursor-not-allowed"
            :class="{ '!border-danger focus:!shadow-[0_0_0_3px_rgb(220_38_38_/_0.14)]': error }"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1.5 text-xs text-danger">{{ error }}</p>
        <p v-else-if="hint" class="mt-1.5 text-xs text-on-surface-muted">{{ hint }}</p>
    </div>
</template>
