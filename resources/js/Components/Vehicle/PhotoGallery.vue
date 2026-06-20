<script setup>
import { ref } from 'vue'
import { mediaUrl } from '@/lib/mediaUrl'

const props = defineProps({
    photos: { type: Array, default: () => [] },
})

const activeIndex = ref(0)

const setActive = (index) => { activeIndex.value = index }
const next = () => { activeIndex.value = (activeIndex.value + 1) % props.photos.length }
const prev = () => { activeIndex.value = (activeIndex.value - 1 + props.photos.length) % props.photos.length }
const photoSrc = (photo) => mediaUrl(photo?.path)
</script>

<template>
    <div v-if="photos.length > 0">
        <div class="relative aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden">
            <img :src="photoSrc(photos[activeIndex])" :alt="`Photo ${activeIndex + 1}`" class="w-full h-full object-cover" />
            <button
                v-if="photos.length > 1"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 text-white p-2 rounded-full hover:bg-black/60"
                @click="prev"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button
                v-if="photos.length > 1"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 text-white p-2 rounded-full hover:bg-black/60"
                @click="next"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded">
                {{ activeIndex + 1 }} / {{ photos.length }}
            </div>
        </div>
        <div v-if="photos.length > 1" class="flex gap-2 mt-3 overflow-x-auto pb-2">
            <button
                v-for="(photo, i) in photos"
                :key="i"
                class="w-20 h-15 rounded-lg overflow-hidden shrink-0 border-2 transition"
                :class="i === activeIndex ? 'border-blue-500' : 'border-transparent'"
                @click="setActive(i)"
            >
                <img :src="photoSrc(photo)" class="w-full h-full object-cover" />
            </button>
        </div>
    </div>
    <div v-else class="aspect-[4/3] bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
        Нет фотографий
    </div>
</template>
