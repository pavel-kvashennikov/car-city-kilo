<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Tag, BookOpen, Clock } from 'lucide-vue-next';

const props = defineProps({
    posts:      { type: Object, default: () => ({ data: [], links: [] }) },
    categories: { type: Array,  default: () => [] },
    tags:       { type: Array,  default: () => [] },
    filters:    { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
let searchTimer = null;

watch(search, (val) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get('/blog', { ...props.filters, search: val || undefined }, { preserveState: true, replace: true });
    }, 400);
});

const filterBy = (key, val) => {
    const params = { ...props.filters, [key]: val || undefined };
    if (key === 'category') delete params.tag;
    if (key === 'tag') delete params.category;
    router.get('/blog', params, { preserveState: true, replace: true });
};

const fmtDate = (d) => {
    if (!d) return '';
    return new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric' });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-10 space-y-10">

            <!-- Header -->
            <div class="text-center space-y-3">
                <h1 class="text-4xl font-extrabold text-on-surface">Блог</h1>
                <p class="text-on-surface-muted text-lg max-w-xl mx-auto">
                    Советы по выбору авто, обзоры, новости рынка и полезные статьи
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Sidebar -->
                <aside class="lg:w-64 shrink-0 space-y-5">

                    <!-- Search -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-muted" />
                        <input v-model="search" type="text" placeholder="Поиск по статьям..."
                            class="input-field !pl-9 w-full !text-sm" />
                    </div>

                    <!-- Categories -->
                    <div class="card p-4 space-y-2">
                        <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wide mb-3">Категории</p>
                        <button @click="filterBy('category', '')"
                            class="w-full text-left text-sm px-2 py-1.5 rounded-lg transition"
                            :class="!filters.category ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface hover:bg-surface-muted'">
                            Все статьи
                        </button>
                        <button v-for="cat in categories" :key="cat.id"
                            @click="filterBy('category', cat.slug)"
                            class="w-full text-left text-sm px-2 py-1.5 rounded-lg flex items-center justify-between transition"
                            :class="filters.category === cat.slug ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface hover:bg-surface-muted'">
                            <span>{{ cat.name }}</span>
                            <span class="text-xs text-on-surface-muted">{{ cat.posts_count }}</span>
                        </button>
                    </div>

                    <!-- Tags -->
                    <div v-if="tags.length" class="card p-4">
                        <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wide mb-3">Теги</p>
                        <div class="flex flex-wrap gap-1.5">
                            <button v-for="tag in tags" :key="tag.id"
                                @click="filterBy('tag', filters.tag === tag.slug ? '' : tag.slug)"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition"
                                :class="filters.tag === tag.slug
                                    ? 'bg-primary text-white'
                                    : 'bg-surface-muted text-on-surface hover:bg-primary/10 hover:text-primary'">
                                <Tag class="w-3 h-3" />{{ tag.name }}
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Posts grid -->
                <div class="flex-1 space-y-6">

                    <div v-if="!posts.data?.length" class="card p-20 text-center">
                        <BookOpen class="w-12 h-12 text-outline mx-auto mb-3" />
                        <p class="text-on-surface-muted font-medium">Статьи не найдены</p>
                        <p class="text-sm text-on-surface-muted mt-1">Попробуйте изменить фильтры</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <Link v-for="post in posts.data" :key="post.id"
                            :href="`/blog/${post.slug}`"
                            class="card group overflow-hidden hover:shadow-md transition-shadow flex flex-col">

                            <!-- Thumbnail -->
                            <div class="aspect-[16/9] bg-surface-muted overflow-hidden">
                                <img v-if="post.thumbnail"
                                    :src="`/storage/${post.thumbnail}`"
                                    :alt="post.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <BookOpen class="w-10 h-10 text-outline" />
                                </div>
                            </div>

                            <div class="p-4 flex flex-col flex-1 space-y-2">
                                <!-- Category -->
                                <div class="flex items-center gap-2">
                                    <span v-if="post.category"
                                        class="text-xs font-semibold text-primary bg-primary/10 px-2 py-0.5 rounded-full">
                                        {{ post.category.name }}
                                    </span>
                                    <span class="text-xs text-on-surface-muted ml-auto flex items-center gap-1">
                                        <Clock class="w-3 h-3" /> {{ post.reading_time_minutes ?? '~5' }} мин
                                    </span>
                                </div>

                                <!-- Title -->
                                <h2 class="font-bold text-on-surface group-hover:text-primary transition-colors line-clamp-2">
                                    {{ post.title }}
                                </h2>

                                <!-- Excerpt -->
                                <p v-if="post.excerpt" class="text-sm text-on-surface-muted line-clamp-2 flex-1">
                                    {{ post.excerpt }}
                                </p>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-2 border-t border-outline">
                                    <span class="text-xs text-on-surface-muted">{{ fmtDate(post.published_at) }}</span>
                                    <div class="flex gap-1 flex-wrap justify-end">
                                        <span v-for="tag in post.tags?.slice(0, 2)" :key="tag.id"
                                            class="text-xs bg-surface-muted text-on-surface-muted px-1.5 py-0.5 rounded">
                                            #{{ tag.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <Pagination :links="posts.links ?? []" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
