<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    service: { type: Object, required: true },
})
</script>

<template>
    <Link :href="`/services/${service.slug}`" class="block bg-white rounded-xl shadow hover:shadow-lg transition p-6">
        <div class="flex items-start gap-4">
            <div v-if="service.company?.logo_path" class="w-16 h-16 rounded-lg overflow-hidden shrink-0">
                <img :src="service.company.logo_path" :alt="service.company.name" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-16 h-16 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <span class="text-blue-600 font-bold text-xl">{{ service.company?.name?.charAt(0) }}</span>
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 truncate">{{ service.company?.name }}</h3>
                <p v-if="service.description" class="text-sm text-gray-500 mt-1 line-clamp-2">
                    {{ service.description }}
                </p>
                <div v-if="service.specializations" class="flex flex-wrap gap-1 mt-2">
                    <span
                        v-for="spec in (service.specializations || []).slice(0, 3)"
                        :key="spec"
                        class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"
                    >
                        {{ spec }}
                    </span>
                </div>
            </div>
        </div>
    </Link>
</template>
