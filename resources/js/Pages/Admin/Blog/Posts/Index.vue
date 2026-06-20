<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Search, BookOpen, Eye, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    posts:   { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
let timer = null;

watch([search, status], ([s, st]) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get('/admin/blog/posts', { search: s || undefined, status: st || undefined },
            { preserveState: true, replace: true });
    }, 350);
});

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';

const destroy = (id) => {
    if (confirm('Удалить статью?')) router.delete(`/admin/blog/posts/${id}`);
};
</script>

<template>
    <AdminLayout>
        <div class="space-y-5">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Статьи блога</h1>
                    <p class="page-subtitle">Управление публикациями</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link href="/admin/blog/categories" class="btn-secondary !text-sm">Категории и теги</Link>
                    <Link href="/admin/blog/posts/create" class="btn-primary !text-sm">
                        <Plus class="w-4 h-4" /> Написать статью
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-muted" />
                    <input v-model="search" placeholder="Поиск..." class="input-field !pl-9 !text-sm w-56" />
                </div>
                <select v-model="status" class="input-field !text-sm !py-1.5 w-40">
                    <option value="">Все статусы</option>
                    <option value="published">Опубликованы</option>
                    <option value="draft">Черновики</option>
                </select>
            </div>

            <div v-if="!posts.data?.length" class="card p-16 text-center">
                <BookOpen class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted">Статей нет</p>
            </div>

            <div v-else class="card overflow-hidden">
                <div class="divide-y divide-outline">
                    <div v-for="post in posts.data" :key="post.id"
                        class="flex items-center gap-4 px-5 py-3.5 hover:bg-surface-muted/40 transition-colors">

                        <!-- Thumb -->
                        <div class="w-14 h-10 rounded-lg overflow-hidden bg-surface-muted shrink-0">
                            <img v-if="post.thumbnail" :src="`/storage/${post.thumbnail}`"
                                class="w-full h-full object-cover" />
                            <BookOpen v-else class="w-5 h-5 text-outline m-auto mt-2.5" />
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-on-surface truncate">{{ post.title }}</p>
                            <p class="text-xs text-on-surface-muted mt-0.5">
                                {{ post.category?.name ?? 'Без категории' }}
                                · {{ post.author?.name }}
                                · {{ fmtDate(post.published_at ?? post.created_at) }}
                            </p>
                        </div>

                        <!-- Status -->
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                            :class="post.status === 'published' ? 'badge-success' : 'badge-neutral'">
                            {{ post.status === 'published' ? 'Опубликована' : 'Черновик' }}
                        </span>

                        <!-- Views -->
                        <span class="text-xs text-on-surface-muted flex items-center gap-1 shrink-0">
                            <Eye class="w-3.5 h-3.5" /> {{ post.views }}
                        </span>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            <Link :href="`/blog/${post.slug}`" target="_blank"
                                class="p-1.5 rounded-lg hover:bg-surface-muted transition-colors" title="Открыть">
                                <Eye class="w-4 h-4 text-on-surface-muted" />
                            </Link>
                            <Link :href="`/admin/blog/posts/${post.id}/edit`"
                                class="p-1.5 rounded-lg hover:bg-surface-muted transition-colors" title="Редактировать">
                                <Pencil class="w-4 h-4 text-on-surface-muted" />
                            </Link>
                            <button @click="destroy(post.id)"
                                class="p-1.5 rounded-lg hover:bg-surface-muted transition-colors" title="Удалить">
                                <Trash2 class="w-4 h-4 text-on-surface-muted hover:text-danger" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <Pagination :links="posts.links ?? []" />
        </div>
    </AdminLayout>
</template>
