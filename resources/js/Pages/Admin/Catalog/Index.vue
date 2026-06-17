<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'

defineProps({
    brandsCount: { type: Number, default: 0 },
    modelsCount: { type: Number, default: 0 },
    categoriesCount: { type: Number, default: 0 },
})
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Каталог / Справочники</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm text-gray-500">Марки авто</p>
                <p class="text-3xl font-bold mt-1">{{ brandsCount }}</p>
                <a href="/admin/catalog/brands" class="text-blue-600 text-sm hover:underline mt-2 block">Управление</a>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm text-gray-500">Модели</p>
                <p class="text-3xl font-bold mt-1">{{ modelsCount }}</p>
                <a href="/admin/catalog/models" class="text-blue-600 text-sm hover:underline mt-2 block">Управление</a>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-sm text-gray-500">Категории запчастей</p>
                <p class="text-3xl font-bold mt-1">{{ categoriesCount }}</p>
                <a href="/admin/catalog/categories" class="text-blue-600 text-sm hover:underline mt-2 block">Управление</a>
            </div>
        </div>
    </AdminLayout>
</template>
