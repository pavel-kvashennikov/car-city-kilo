<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'
import MarketMap from '@/Components/Map/MarketMap.vue'

defineProps({
    zones: { type: Array, default: () => [] },
})
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Редактор карты рынка</h1>
        <p class="text-gray-500 mb-4">Управление зонами и расположением резидентов на площадке</p>
        <MarketMap :zones="zones" editable />
    </AdminLayout>
</template>
