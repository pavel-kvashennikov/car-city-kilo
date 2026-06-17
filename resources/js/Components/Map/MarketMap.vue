<script setup>
import { ref, onMounted, computed } from 'vue'
import LocationPin from './LocationPin.vue'

const props = defineProps({
    zones: { type: Array, default: () => [] },
    locations: { type: Array, default: () => [] },
})

const emit = defineEmits(['locationClick'])

const scale = ref(1)
const offset = ref({ x: 0, y: 0 })
const dragging = ref(false)
const lastPos = ref({ x: 0, y: 0 })

const zoomIn = () => { scale.value = Math.min(scale.value * 1.2, 5) }
const zoomOut = () => { scale.value = Math.max(scale.value / 1.2, 0.5) }

const onMouseDown = (e) => {
    dragging.value = true
    lastPos.value = { x: e.clientX, y: e.clientY }
}

const onMouseMove = (e) => {
    if (!dragging.value) return
    offset.value.x += e.clientX - lastPos.value.x
    offset.value.y += e.clientY - lastPos.value.y
    lastPos.value = { x: e.clientX, y: e.clientY }
}

const onMouseUp = () => { dragging.value = false }

const mapTransform = computed(() =>
    `translate(${offset.value.x}px, ${offset.value.y}px) scale(${scale.value})`
)
</script>

<template>
    <div class="relative overflow-hidden rounded-xl border bg-gray-50" style="height: 600px">
        <div class="absolute top-4 right-4 z-10 flex flex-col gap-1">
            <button class="w-8 h-8 bg-white rounded shadow flex items-center justify-center hover:bg-gray-50" @click="zoomIn">+</button>
            <button class="w-8 h-8 bg-white rounded shadow flex items-center justify-center hover:bg-gray-50" @click="zoomOut">−</button>
        </div>

        <div
            class="w-full h-full cursor-grab active:cursor-grabbing"
            :style="{ transform: mapTransform, transformOrigin: '0 0' }"
            @mousedown="onMouseDown"
            @mousemove="onMouseMove"
            @mouseup="onMouseUp"
            @mouseleave="onMouseUp"
        >
            <svg v-for="zone in zones" :key="zone.id" class="absolute">
                <polygon
                    v-if="zone.polygon_coords"
                    :points="zone.polygon_coords.map(p => `${p.x},${p.y}`).join(' ')"
                    :fill="zone.color || '#e0e7ff'"
                    stroke="#6366f1"
                    stroke-width="2"
                    opacity="0.3"
                />
            </svg>

            <LocationPin
                v-for="loc in locations"
                :key="loc.id"
                :location="loc"
                @click="emit('locationClick', loc)"
            />
        </div>
    </div>
</template>
