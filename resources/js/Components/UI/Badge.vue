<script setup>
defineProps({
    variant: { type: String, default: 'default' },
})

const variantClasses = {
    default: 'badge-neutral',
    success: 'badge-success',
    warning: 'badge-warning',
    danger: 'badge-danger',
    info: 'badge-primary',
}
</script>

<template>
    <span :class="['badge', variantClasses[variant]]">
        <slot />
    </span>
</template>
