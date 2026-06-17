<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue'
import MarketMap from '@/Components/Map/MarketMap.vue'

defineProps({
    zones: { type: Array, default: () => [] },
    locations: { type: Array, default: () => [] },
})
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6">Карта рынка</h1>
        <MarketMap :zones="zones" :locations="locations" />
    </AdminLayout>
</template>
