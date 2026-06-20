<script setup>
import AdminLayout from '@/Components/Shared/AdminLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    tags:       { type: Array, default: () => [] },
});

const form = useForm({
    title:                '',
    slug:                 '',
    excerpt:              '',
    content:              '',
    category_id:          '',
    status:               'draft',
    published_at:         '',
    reading_time_minutes: '',
    tags:                 [],
    thumbnail:            null,
});

const onThumb = (e) => { form.thumbnail = e.target.files[0] ?? null; };
const toggleTag = (id) => {
    const idx = form.tags.indexOf(id);
    if (idx >= 0) form.tags.splice(idx, 1);
    else form.tags.push(id);
};

const submit = () => form.post('/admin/blog/posts', { forceFormData: true });
</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl space-y-6">

            <div>
                <Link href="/admin/blog/posts"
                    class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку статей
                </Link>
                <h1 class="page-title">Новая статья</h1>
            </div>

            <form class="space-y-5" @submit.prevent="submit">

                <!-- Main -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold border-b border-outline pb-3">Основное</h2>

                    <Input v-model="form.title" label="Заголовок" :error="form.errors.title" required />
                    <Input v-model="form.slug" label="Slug (URL-часть, заполняется автоматически)"
                        :error="form.errors.slug" placeholder="my-article-slug" />

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                            Краткое описание (анонс)
                        </label>
                        <textarea v-model="form.excerpt" rows="2"
                            class="input-field w-full resize-none text-sm"
                            placeholder="1-2 предложения о чём статья..."></textarea>
                        <p v-if="form.errors.excerpt" class="text-xs text-danger mt-1">{{ form.errors.excerpt }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                            Содержимое статьи <span class="text-danger">*</span>
                        </label>
                        <textarea v-model="form.content" rows="18"
                            class="input-field w-full resize-y font-mono text-sm"
                            placeholder="Поддерживается HTML..."></textarea>
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
                                <option value="published">Опубликовать</option>
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
                            type="number" min="1" :error="form.errors.reading_time_minutes"
                            placeholder="Оставьте пустым — рассчитается автоматически" />
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                            Обложка статьи
                        </label>
                        <input type="file" accept="image/*" @change="onThumb"
                            class="block w-full text-sm text-on-surface-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-surface-muted file:text-on-surface hover:file:bg-surface-dim cursor-pointer" />
                        <p v-if="form.errors.thumbnail" class="text-xs text-danger mt-1">{{ form.errors.thumbnail }}</p>
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

                <div class="card p-4 flex items-center gap-3">
                    <Button type="submit" :loading="form.processing">
                        {{ form.status === 'published' ? 'Опубликовать' : 'Сохранить черновик' }}
                    </Button>
                    <Link href="/admin/blog/posts" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
