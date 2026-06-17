<script setup>
defineProps({
    location: { type: Object, required: true },
})

defineEmits(['click'])

const statusColors = {
    available: 'bg-green-500',
    occupied: 'bg-blue-500',
    reserved: 'bg-yellow-500',
    maintenance: 'bg-red-500',
}
</script>

<template>
    <div
        class="absolute cursor-pointer group"
        :style="{ left: `${location.coordinates?.x || 0}px`, top: `${location.coordinates?.y || 0}px` }"
        @click="$emit('click')"
    >
        <div
            class="w-6 h-6 rounded-full border-2 border-white shadow-md flex items-center justify-center"
            :class="statusColors[location.status] || 'bg-gray-400'"
        >
            <span class="text-white text-[10px] font-bold">{{ location.identifier }}</span>
        </div>
        <div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-white shadow-lg rounded-lg p-2 text-xs whitespace-nowrap z-10">
            <p class="font-medium">{{ location.identifier }}</p>
            <p class="text-gray-500">{{ location.location_type }}</p>
            <p v-if="location.company" class="text-blue-600">{{ location.company.name }}</p>
        </div>
    </div>
</template>
