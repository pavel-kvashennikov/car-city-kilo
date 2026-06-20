<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Clock, Eye, Calendar, Tag, ChevronLeft, BookOpen } from 'lucide-vue-next';

const props = defineProps({
    post:    { type: Object, required: true },
    related: { type: Array,  default: () => [] },
});

const fmtDate = (d) => {
    if (!d) return '';
    return new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: 'long', year: 'numeric' });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 py-10">

            <!-- Back -->
            <Link href="/blog"
                class="inline-flex items-center gap-1.5 text-sm text-on-surface-muted hover:text-primary mb-6 transition-colors">
                <ChevronLeft class="w-4 h-4" /> Назад к блогу
            </Link>

            <article class="space-y-6">

                <!-- Header -->
                <header class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <Link v-if="post.category"
                            :href="`/blog?category=${post.category.slug}`"
                            class="text-sm font-semibold text-primary bg-primary/10 px-3 py-1 rounded-full hover:bg-primary/20 transition">
                            {{ post.category.name }}
                        </Link>
                        <span class="text-sm text-on-surface-muted flex items-center gap-1">
                            <Calendar class="w-4 h-4" /> {{ fmtDate(post.published_at) }}
                        </span>
                        <span class="text-sm text-on-surface-muted flex items-center gap-1">
                            <Clock class="w-4 h-4" /> {{ post.reading_time_minutes ?? '~5' }} мин чтения
                        </span>
                        <span class="text-sm text-on-surface-muted flex items-center gap-1">
                            <Eye class="w-4 h-4" /> {{ post.views }} просмотров
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-on-surface leading-tight">
                        {{ post.title }}
                    </h1>

                    <p v-if="post.excerpt" class="text-lg text-on-surface-muted">{{ post.excerpt }}</p>

                    <!-- Author -->
                    <div class="flex items-center gap-3 pt-2">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                            {{ post.author?.name?.charAt(0) ?? '?' }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">{{ post.author?.name }}</p>
                            <p class="text-xs text-on-surface-muted">Автор</p>
                        </div>
                    </div>
                </header>

                <!-- Thumbnail -->
                <div v-if="post.thumbnail" class="rounded-2xl overflow-hidden aspect-[16/7]">
                    <img :src="`/storage/${post.thumbnail}`" :alt="post.title" class="w-full h-full object-cover" />
                </div>

                <!-- Content -->
                <div class="card p-6 sm:p-8 prose prose-lg max-w-none
                    prose-headings:text-on-surface prose-headings:font-bold
                    prose-p:text-on-surface prose-p:leading-relaxed
                    prose-a:text-primary prose-a:no-underline hover:prose-a:underline
                    prose-strong:text-on-surface
                    prose-ul:text-on-surface prose-ol:text-on-surface
                    prose-blockquote:border-primary prose-blockquote:text-on-surface-muted
                    prose-code:text-primary prose-code:bg-surface-muted prose-code:px-1 prose-code:rounded
                    prose-img:rounded-xl"
                    v-html="post.content" />

                <!-- Tags -->
                <div v-if="post.tags?.length" class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-on-surface-muted">Теги:</span>
                    <Link v-for="tag in post.tags" :key="tag.id"
                        :href="`/blog?tag=${tag.slug}`"
                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-surface-muted text-on-surface hover:bg-primary/10 hover:text-primary transition">
                        <Tag class="w-3.5 h-3.5" /> {{ tag.name }}
                    </Link>
                </div>
            </article>

            <!-- Related posts -->
            <section v-if="related.length" class="mt-12 space-y-5">
                <h2 class="text-xl font-bold text-on-surface">Похожие статьи</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <Link v-for="rp in related" :key="rp.id"
                        :href="`/blog/${rp.slug}`"
                        class="card group overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-[16/9] bg-surface-muted overflow-hidden">
                            <img v-if="rp.thumbnail"
                                :src="`/storage/${rp.thumbnail}`"
                                :alt="rp.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <BookOpen class="w-8 h-8 text-outline" />
                            </div>
                        </div>
                        <div class="p-3 space-y-1">
                            <p class="text-xs text-on-surface-muted">{{ fmtDate(rp.published_at) }}</p>
                            <h3 class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2">
                                {{ rp.title }}
                            </h3>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
