<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    service: { type: Object, required: true },
})
</script>

<template>
    <Link
        :href="`/catalog/services/${service.slug}`"
        class="card card-hover p-6 group block"
    >
        <div class="flex items-start gap-4">
            <div v-if="service.company?.logo_path" class="w-16 h-16 rounded-xl overflow-hidden shrink-0 shadow-md ring-2 ring-outline/30">
                <img :src="service.company.logo_path" :alt="service.company.name" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shrink-0 shadow-md">
                <span class="text-white font-bold text-xl">{{ service.company?.name?.charAt(0) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-on-surface truncate group-hover:text-primary transition-colors">
                    {{ service.company?.name }}
                </h3>
                <p v-if="service.description" class="text-sm text-on-surface-muted mt-1.5 line-clamp-2 leading-relaxed">
                    {{ service.description }}
                </p>
                <div v-if="service.specializations?.length" class="flex flex-wrap gap-1.5 mt-3">
                    <span
                        v-for="spec in service.specializations.slice(0, 3)"
                        :key="spec"
                        class="badge badge-neutral text-xs"
                    >
                        {{ spec }}
                    </span>
                </div>
            </div>
        </div>
    </Link>
</template>
