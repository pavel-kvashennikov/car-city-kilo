<script setup>
import { vMaska } from 'maska/vue';
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '+7 (___) ___-__-__' },
    required:    { type: Boolean, default: false },
    disabled:    { type: Boolean, default: false },
    inputClass:  { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const value = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});
</script>

<template>
    <input
        v-maska="'+7 (###) ###-##-##'"
        v-model="value"
        type="tel"
        inputmode="numeric"
        autocomplete="tel"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="inputClass"
    />
</template>
