<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { Link } from '@inertiajs/vue3';
import { Plus, Clock, Wrench, ToggleLeft, ToggleRight } from 'lucide-vue-next';

defineProps({
    items: { type: Object, default: () => ({ data: [], links: [] }) },
});

const fmt = (v) => v ? new Intl.NumberFormat('ru-RU').format(v) + ' ₽' : null;

function priceLabel(item) {
    if (item.price_fixed) return fmt(item.price_fixed);
    if (item.price_from && item.price_to) return `от ${fmt(item.price_from)} до ${fmt(item.price_to)}`;
    if (item.price_from) return `от ${fmt(item.price_from)}`;
    if (item.price_per_hour) return `${fmt(item.price_per_hour)}/час`;
    return '—';
}
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Прайс-лист услуг</h1>
                    <p class="page-subtitle">Управление перечнем и стоимостью услуг сервиса</p>
                </div>
                <Link href="/cabinet/service/items/create" class="btn-primary !text-sm">
                    <Plus class="w-4 h-4" /> Добавить услугу
                </Link>
            </div>

            <!-- Empty state -->
            <div v-if="!items.data?.length && !items.length" class="card p-16 text-center">
                <Wrench class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">Услуги не добавлены</p>
                <p class="text-sm text-on-surface-muted mt-1 mb-4">
                    Добавьте услуги в прайс-лист — они отобразятся на странице вашего сервиса
                </p>
                <Link href="/cabinet/service/items/create" class="btn-primary !text-sm inline-flex">
                    <Plus class="w-4 h-4" /> Добавить первую услугу
                </Link>
            </div>

            <!-- List -->
            <div v-else class="card overflow-hidden">
                <div class="divide-y divide-outline">
                    <div
                        v-for="item in (items.data ?? items)"
                        :key="item.id"
                        class="flex items-center gap-4 px-5 py-4 hover:bg-surface-muted/40 transition-colors"
                        :class="{ 'opacity-60': !item.is_active }"
                    >
                        <!-- Status icon -->
                        <div class="shrink-0">
                            <component :is="item.is_active ? ToggleRight : ToggleLeft"
                                class="w-5 h-5"
                                :class="item.is_active ? 'text-tertiary' : 'text-outline'" />
                        </div>

                        <!-- Name & category -->
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-on-surface text-sm">{{ item.name }}</p>
                            <div class="flex items-center gap-3 mt-0.5">
                                <span v-if="item.category?.name"
                                    class="text-xs text-on-surface-muted">
                                    {{ item.category.name }}
                                </span>
                                <span v-if="item.description"
                                    class="text-xs text-on-surface-muted line-clamp-1 truncate max-w-xs">
                                    {{ item.description }}
                                </span>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div v-if="item.duration_minutes" class="hidden sm:flex items-center gap-1 text-sm text-on-surface-muted shrink-0">
                            <Clock class="w-3.5 h-3.5" />
                            {{ item.duration_minutes }} мин
                        </div>

                        <!-- Price -->
                        <div class="text-right shrink-0">
                            <p class="font-semibold text-sm text-on-surface">{{ priceLabel(item) }}</p>
                            <p v-if="!item.is_active" class="text-xs text-on-surface-muted">Неактивна</p>
                        </div>

                        <!-- Edit link -->
                        <Link :href="`/cabinet/service/items/${item.id}/edit`"
                            class="text-xs font-semibold text-primary hover:underline shrink-0">
                            Изменить
                        </Link>
                    </div>
                </div>
            </div>

            <Pagination v-if="items.links" :links="items.links" />
        </div>
    </CabinetLayout>
</template>
