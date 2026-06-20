<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Trash2, ExternalLink } from 'lucide-vue-next';

const props = defineProps({
    post:       { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    tags:       { type: Array, default: () => [] },
});

const form = useForm({
    _method:              'PUT',
    title:                props.post.title ?? '',
    slug:                 props.post.slug ?? '',
    excerpt:              props.post.excerpt ?? '',
    content:              props.post.content ?? '',
    category_id:          props.post.category_id ?? '',
    status:               props.post.status ?? 'draft',
    published_at:         props.post.published_at
        ? new Date(props.post.published_at).toISOString().slice(0, 16)
        : '',
    reading_time_minutes: props.post.reading_time_minutes ?? '',
    tags:                 props.post.tags?.map(t => t.id) ?? [],
    thumbnail:            null,
});

const onThumb = (e) => { form.thumbnail = e.target.files[0] ?? null; };
const toggleTag = (id) => {
    const idx = form.tags.indexOf(id);
    if (idx >= 0) form.tags.splice(idx, 1);
    else form.tags.push(id);
};

const submit = () => form.post(`/admin/blog/posts/${props.post.id}`, { forceFormData: true });
const destroy = () => {
    if (confirm('Удалить статью безвозвратно?'))
        router.delete(`/admin/blog/posts/${props.post.id}`);
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl space-y-6">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/admin/blog/posts"
                        class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку статей
                    </Link>
                    <h1 class="page-title">Редактирование статьи</h1>
                    <p class="text-sm text-on-surface-muted mt-1 truncate max-w-md">{{ post.title }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <Link v-if="post.status === 'published'" :href="`/blog/${post.slug}`" target="_blank"
                        class="btn-secondary !text-sm">
                        <ExternalLink class="w-3.5 h-3.5" /> Смотреть
                    </Link>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                        :class="post.status === 'published' ? 'badge-success' : 'badge-neutral'">
                        {{ post.status === 'published' ? 'Опубликована' : 'Черновик' }}
                    </span>
                </div>
            </div>

            <form class="space-y-5" @submit.prevent="submit">

                <!-- Main -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold border-b border-outline pb-3">Основное</h2>

                    <Input v-model="form.title" label="Заголовок" :error="form.errors.title" required />
                    <Input v-model="form.slug" label="Slug" :error="form.errors.slug" />

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                            Краткое описание (анонс)
                        </label>
                        <textarea v-model="form.excerpt" rows="2"
                            class="input-field w-full resize-none text-sm"></textarea>
                        <p v-if="form.errors.excerpt" class="text-xs text-danger mt-1">{{ form.errors.excerpt }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                            Содержимое статьи
                        </label>
                        <textarea v-model="form.content" rows="20"
                            class="input-field w-full resize-y font-mono text-sm"></textarea>
                        <p v-if="form.errors.content" class="text-xs text-danger mt-1">{{ form.errors.content }}</p>
                    </div>
                </div>

                <!-- Settings -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold border-b border-outline pb-3">Настройки публикации</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Статус
                            </label>
                            <select v-model="form.status" class="input-field !text-sm w-full">
                                <option value="draft">Черновик</option>
                                <option value="published">Опубликована</option>
                            </select>
                        </div>

                        <Input v-model="form.published_at" label="Дата публикации" type="datetime-local"
                            :error="form.errors.published_at" />

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Категория
                            </label>
                            <select v-model="form.category_id" class="input-field !text-sm w-full">
                                <option value="">Без категории</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <Input v-model="form.reading_time_minutes" label="Время чтения (мин)"
                            type="number" min="1" :error="form.errors.reading_time_minutes" />
                    </div>

                    <!-- Thumbnail -->
                    <div class="flex items-start gap-4">
                        <div v-if="post.thumbnail" class="w-24 h-16 rounded-lg overflow-hidden bg-surface-muted shrink-0">
                            <img :src="`/storage/${post.thumbnail}`" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                {{ post.thumbnail ? 'Заменить обложку' : 'Обложка статьи' }}
                            </label>
                            <input type="file" accept="image/*" @change="onThumb"
                                class="block w-full text-sm text-on-surface-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-surface-muted file:text-on-surface hover:file:bg-surface-dim cursor-pointer" />
                            <p v-if="form.errors.thumbnail" class="text-xs text-danger mt-1">{{ form.errors.thumbnail }}</p>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div v-if="tags.length">
                        <label class="block text-xs font-semibold text-on-surface-muted mb-2 uppercase tracking-wide">Теги</label>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="tag in tags" :key="tag.id" type="button"
                                @click="toggleTag(tag.id)"
                                class="px-3 py-1 rounded-full text-xs font-medium transition"
                                :class="form.tags.includes(tag.id)
                                    ? 'bg-primary text-white'
                                    : 'bg-surface-muted text-on-surface hover:bg-primary/10 hover:text-primary'">
                                #{{ tag.name }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/admin/blog/posts" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy"
                        class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить статью
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
