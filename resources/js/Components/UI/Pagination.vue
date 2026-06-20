<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, default: () => [] },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-center gap-1">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                class="min-w-[36px] h-9 flex items-center justify-center px-3 rounded-lg text-sm border transition-colors"
                :class="link.active
                    ? 'border-primary bg-primary-light text-primary font-semibold'
                    : 'border-outline text-on-surface-muted hover:border-primary hover:text-primary'"
                v-html="link.label"
            />
            <span
                v-else
                class="min-w-[36px] h-9 flex items-center justify-center px-3 rounded-lg text-sm border border-transparent text-outline cursor-not-allowed"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
