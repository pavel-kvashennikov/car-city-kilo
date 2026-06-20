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
        <div class="container-app max-w-4xl py-8">
            <h1 class="page-title mb-6">Избранное</h1>

            <div v-if="!favorites.data?.length" class="card p-12 text-center text-on-surface-muted">
                В избранном пока ничего нет
            </div>

            <div v-else class="space-y-3">
                <Link
                    v-for="item in favorites.data"
                    :key="item.id"
                    :href="itemUrl(item)"
                    class="card card-hover p-5 block"
                >
                    <p class="font-semibold text-on-surface">{{ itemTitle(item) }}</p>
                    <p class="text-sm text-on-surface-muted mt-1">Добавлено {{ new Date(item.created_at).toLocaleDateString('ru-RU') }}</p>
                </Link>
            </div>

            <div class="mt-6">
                <Pagination :links="favorites.links" />
            </div>
        </div>
    </AppLayout>
</template>
