<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/UI/Pagination.vue'

defineProps({
    companies: { type: Object, default: () => ({ data: [], links: [] }) },
})
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Компании</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link
                    v-for="company in companies.data"
                    :key="company.id"
                    :href="`/companies/${company.slug}`"
                    class="bg-white rounded-xl shadow hover:shadow-lg transition p-6"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-xl">{{ company.name?.charAt(0) }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ company.name }}</h3>
                            <p v-if="company.description" class="text-sm text-gray-500 mt-1 line-clamp-2">
                                {{ company.description }}
                            </p>
                        </div>
                    </div>
                    <div v-if="company.business_profiles" class="flex gap-2 mt-4">
                        <span
                            v-for="profile in company.business_profiles"
                            :key="profile.id"
                            class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full"
                        >
                            {{ profile.profile_type }}
                        </span>
                    </div>
                </Link>
            </div>
            <div class="mt-8">
                <Pagination :links="companies.links" />
            </div>
        </div>
    </AppLayout>
</template>
