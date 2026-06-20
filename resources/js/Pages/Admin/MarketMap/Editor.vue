<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import MarketMap from '@/Components/Map/MarketMap.vue';

defineProps({
    zones: { type: Array, default: () => [] },
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">
            <div>
                <h1 class="page-title">Карта рынка</h1>
                <p class="page-subtitle">Зоны и расположение резидентов на площадке</p>
            </div>

            <div class="card p-5">
                <MarketMap :zones="zones" editable />
            </div>
        </div>
    </AdminLayout>
</template>
