<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: { type: Array, default: () => [] },
})
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-center gap-1">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                class="px-3 py-2 text-sm rounded-lg border transition"
                :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
                v-html="link.label"
            />
            <span
                v-else
                class="px-3 py-2 text-sm text-gray-400"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
