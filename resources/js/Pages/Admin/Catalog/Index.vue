<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import { Car, Layers, Package } from 'lucide-vue-next';

defineProps({
    brandsCount: { type: Number, default: 0 },
    modelsCount: { type: Number, default: 0 },
    categoriesCount: { type: Number, default: 0 },
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Каталог и справочники</h1>
                <p class="page-subtitle">Марки, модели и категории запчастей</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shrink-0">
                        <Car class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-on-surface">{{ brandsCount }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">Марки автомобилей</p>
                    </div>
                </div>
                <div class="card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shrink-0">
                        <Layers class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-on-surface">{{ modelsCount }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">Модели</p>
                    </div>
                </div>
                <div class="card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center shrink-0">
                        <Package class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-on-surface">{{ categoriesCount }}</p>
                        <p class="text-xs text-on-surface-muted mt-0.5">Категории запчастей</p>
                    </div>
                </div>
            </div>

            <div class="card p-6 text-sm text-on-surface-muted leading-relaxed">
                Справочники заполнены и используются при создании объявлений и товаров резидентами.
                Добавление новых марок, моделей и категорий выполняется через сидеры на стороне платформы.
            </div>
        </div>
    </AdminLayout>
</template>
