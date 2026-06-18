<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    favorites: { type: Object, default: () => ({ data: [], links: [] }) },
})

const itemTitle = (item) => {
    const f = item.favoriteable
    if (!f) return 'Удалённый объект'
    return f.name || f.title || `${f.brand?.name ?? ''} ${f.car_model?.name ?? ''}`.trim() || `#${f.id}`
}

const itemUrl = (item) => {
    const f = item.favoriteable
    if (!f) return '#'
    if (f.slug && item.favoriteable_type?.includes('Vehicle')) return `/catalog/cars/${f.slug}`
    if (f.slug && item.favoriteable_type?.includes('Product')) return `/catalog/parts/${f.slug}`
    return '#'
}
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Избранное</h1>

            <div v-if="!favorites.data?.length" class="text-center text-gray-500 py-12">
                В избранном пока ничего нет
            </div>

            <div v-else class="space-y-3">
                <Link
                    v-for="item in favorites.data"
                    :key="item.id"
                    :href="itemUrl(item)"
                    class="block bg-white rounded-xl shadow p-4 hover:shadow-md transition"
                >
                    <p class="font-medium">{{ itemTitle(item) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Добавлено {{ new Date(item.created_at).toLocaleDateString('ru-RU') }}</p>
                </Link>
            </div>

            <div class="mt-6">
                <Pagination :links="favorites.links" />
            </div>
        </div>
    </AppLayout>
</template>
