<script setup>
defineProps({
    modelValue: { type: [String, Number, null], default: '' },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    options: { type: Array, default: () => [] },
    optionValue: { type: String, default: 'value' },
    optionLabel: { type: String, default: 'label' },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <select
            :value="modelValue"
            :required="required"
            :disabled="disabled"
            class="input-field disabled:bg-surface-muted disabled:cursor-not-allowed"
            :class="{ '!border-danger focus:!shadow-[0_0_0_3px_rgb(220_38_38_/_0.14)]': error }"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-if="placeholder" value="">{{ placeholder }}</option>
            <slot>
                <option
                    v-for="opt in options"
                    :key="typeof opt === 'object' ? opt[optionValue] : opt"
                    :value="typeof opt === 'object' ? opt[optionValue] : opt"
                >
                    {{ typeof opt === 'object' ? opt[optionLabel] : opt }}
                </option>
            </slot>
        </select>
        <p v-if="error" class="mt-1.5 text-xs text-danger">{{ error }}</p>
    </div>
</template>
