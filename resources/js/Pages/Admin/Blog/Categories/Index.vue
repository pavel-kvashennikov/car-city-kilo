<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronLeft, Plus, Trash2, Tag } from 'lucide-vue-next';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    tags:       { type: Array, default: () => [] },
});

// Category form
const catForm = useForm({ name: '', slug: '', description: '', sort_order: 0 });
const addCat = () => catForm.post('/admin/blog/categories', { onSuccess: () => catForm.reset() });
const destroyCat = (id) => {
    if (confirm('Удалить категорию?')) router.delete(`/admin/blog/categories/${id}`);
};
const toggleCatActive = (cat) => {
    router.put(`/admin/blog/categories/${cat.id}`, { is_active: !cat.is_active });
};

// Tag form
const tagForm = useForm({ name: '', slug: '' });
const addTag = () => tagForm.post('/admin/blog/tags', { onSuccess: () => tagForm.reset() });
const destroyTag = (id) => {
    if (confirm('Удалить тег?')) router.delete(`/admin/blog/tags/${id}`);
};
</script>

<template>
    <AdminLayout>
        <div class="space-y-6 max-w-4xl">

            <div>
                <Link href="/admin/blog/posts"
                    class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К статьям
                </Link>
                <h1 class="page-title">Категории и теги блога</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Categories -->
                <div class="space-y-4">
                    <h2 class="font-semibold text-on-surface">Категории</h2>

                    <!-- Add form -->
                    <form class="card p-4 space-y-3" @submit.prevent="addCat">
                        <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wide">Новая категория</p>
                        <input v-model="catForm.name" placeholder="Название *" required
                            class="input-field w-full !text-sm" />
                        <input v-model="catForm.slug" placeholder="Slug (автоматически)"
                            class="input-field w-full !text-sm" />
                        <textarea v-model="catForm.description" rows="2" placeholder="Описание (необязательно)"
                            class="input-field w-full resize-none !text-sm"></textarea>
                        <div class="flex items-center gap-3">
                            <input v-model.number="catForm.sort_order" type="number" placeholder="Порядок"
                                class="input-field !text-sm w-24" />
                            <button type="submit"
                                class="btn-primary !text-sm flex-1 flex items-center justify-center gap-1.5"
                                :disabled="catForm.processing">
                                <Plus class="w-4 h-4" /> Добавить
                            </button>
                        </div>
                    </form>

                    <!-- List -->
                    <div class="card overflow-hidden">
                        <div v-if="!categories.length" class="p-8 text-center text-on-surface-muted text-sm">
                            Категорий нет
                        </div>
                        <div class="divide-y divide-outline">
                            <div v-for="cat in categories" :key="cat.id"
                                class="flex items-center gap-3 px-4 py-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-on-surface">{{ cat.name }}</p>
                                    <p class="text-xs text-on-surface-muted">
                                        /{{ cat.slug }} · {{ cat.posts_count }} статей
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full cursor-pointer transition"
                                    :class="cat.is_active ? 'badge-success' : 'badge-neutral'"
                                    @click="toggleCatActive(cat)">
                                    {{ cat.is_active ? 'Активна' : 'Скрыта' }}
                                </span>
                                <button @click="destroyCat(cat.id)"
                                    class="p-1 rounded hover:bg-surface-muted transition-colors shrink-0">
                                    <Trash2 class="w-4 h-4 text-on-surface-muted hover:text-danger" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                <div class="space-y-4">
                    <h2 class="font-semibold text-on-surface">Теги</h2>

                    <!-- Add form -->
                    <form class="card p-4 space-y-3" @submit.prevent="addTag">
                        <p class="text-xs font-semibold text-on-surface-muted uppercase tracking-wide">Новый тег</p>
                        <input v-model="tagForm.name" placeholder="Название тега *" required
                            class="input-field w-full !text-sm" />
                        <input v-model="tagForm.slug" placeholder="Slug (автоматически)"
                            class="input-field w-full !text-sm" />
                        <button type="submit"
                            class="btn-primary !text-sm w-full flex items-center justify-center gap-1.5"
                            :disabled="tagForm.processing">
                            <Plus class="w-4 h-4" /> Добавить тег
                        </button>
                    </form>

                    <!-- List -->
                    <div class="card overflow-hidden">
                        <div v-if="!tags.length" class="p-8 text-center text-on-surface-muted text-sm">
                            Тегов нет
                        </div>
                        <div class="flex flex-wrap gap-2 p-4">
                            <div v-for="tag in tags" :key="tag.id"
                                class="flex items-center gap-1.5 bg-surface-muted rounded-full pl-3 pr-1.5 py-1">
                                <Tag class="w-3 h-3 text-on-surface-muted" />
                                <span class="text-sm font-medium text-on-surface">{{ tag.name }}</span>
                                <span class="text-xs text-on-surface-muted">({{ tag.posts_count }})</span>
                                <button @click="destroyTag(tag.id)"
                                    class="w-5 h-5 flex items-center justify-center rounded-full hover:bg-danger/10 transition-colors">
                                    <Trash2 class="w-3 h-3 text-on-surface-muted hover:text-danger" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
