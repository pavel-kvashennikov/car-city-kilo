<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import { Link, router } from '@inertiajs/vue3'
import { Car, Settings, Heart, Trash2, ExternalLink } from 'lucide-vue-next'

defineProps({
    favorites: { type: Object, default: () => ({ data: [], links: [] }) },
})

const typeLabel = (type) => {
    if (type?.includes('Vehicle')) return 'Автомобиль'
    if (type?.includes('Product')) return 'Запчасть'
    return 'Товар'
}

const typeIcon = (type) => {
    if (type?.includes('Vehicle')) return Car
    return Settings
}

const itemTitle = (item) => {
    const f = item.favoriteable
    if (!f) return 'Удалённый объект'
    if (f.brand?.name || f.car_model?.name) return `${f.brand?.name ?? ''} ${f.car_model?.name ?? ''}`.trim()
    return f.name || f.title || `#${f.id}`
}

const itemSubtitle = (item) => {
    const f = item.favoriteable
    if (!f) return null
    if (f.year) return `${f.year} г. · ${fmt(f.price)}`
    if (f.price_retail) return fmt(f.price_retail)
    return null
}

const itemUrl = (item) => {
    const f = item.favoriteable
    if (!f) return null
    if (item.favoriteable_type?.includes('Vehicle') && f.slug) return `/catalog/cars/${f.slug}`
    if (item.favoriteable_type?.includes('Product') && f.slug) return `/catalog/parts/${f.slug}`
    return null
}

const fmt = (v) => v ? new Intl.NumberFormat('ru-RU').format(v) + ' ₽' : null
const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric' }) : '—'

function removeFavorite(id) {
    router.delete(`/buyer/favorites/${id}`, { preserveScroll: true })
}
</script>

<template>
    <AppLayout>
        <div class="container-app max-w-3xl py-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="page-title">Избранное</h1>
                <span v-if="favorites.data?.length" class="text-sm text-on-surface-muted">
                    {{ favorites.data.length }} {{ favorites.data.length === 1 ? 'объект' : 'объекта' }}
                </span>
            </div>

            <div v-if="!favorites.data?.length" class="card p-16 text-center">
                <Heart class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">В избранном пока ничего нет</p>
                <p class="text-sm text-on-surface-muted mt-1 mb-4">
                    Нажмите на сердечко на карточке товара или автомобиля
                </p>
                <div class="flex gap-2 justify-center flex-wrap">
                    <Link href="/catalog/cars" class="btn-primary text-sm">Смотреть авто</Link>
                    <Link href="/catalog/parts" class="btn-secondary text-sm">Запчасти</Link>
                </div>
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="item in favorites.data"
                    :key="item.id"
                    class="card p-4 flex items-center gap-4"
                >
                    <!-- Icon -->
                    <div class="w-11 h-11 rounded-xl bg-primary-light flex items-center justify-center shrink-0">
                        <component :is="typeIcon(item.favoriteable_type)" class="w-5 h-5 text-primary" />
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs text-on-surface-muted font-semibold uppercase tracking-wide">
                                {{ typeLabel(item.favoriteable_type) }}
                            </span>
                        </div>
                        <p class="font-semibold text-sm text-on-surface truncate">
                            {{ itemTitle(item) }}
                        </p>
                        <div class="flex items-center gap-3 mt-0.5">
                            <span v-if="itemSubtitle(item)" class="text-sm font-bold text-gradient">
                                {{ itemSubtitle(item) }}
                            </span>
                            <span class="text-xs text-on-surface-muted">
                                Добавлено {{ fmtDate(item.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <Link
                            v-if="itemUrl(item)"
                            :href="itemUrl(item)"
                            class="btn-ghost !p-2 !text-primary"
                            title="Открыть"
                        >
                            <ExternalLink class="w-4 h-4" />
                        </Link>
                        <button
                            @click="removeFavorite(item.id)"
                            class="btn-ghost !p-2 !text-danger/70 hover:!text-danger"
                            title="Удалить из избранного"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <Pagination :links="favorites.links" />
            </div>
        </div>
    </AppLayout>
</template>
