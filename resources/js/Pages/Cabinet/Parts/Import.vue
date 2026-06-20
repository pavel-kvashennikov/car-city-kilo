<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft, UploadCloud, FileSpreadsheet, CheckCircle2 } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({ file: null });
const fileName = ref('');
const dragging = ref(false);

const onFile = (e) => {
    const f = e.target.files?.[0] || e.dataTransfer?.files?.[0];
    if (f) {
        form.file = f;
        fileName.value = f.name;
    }
    dragging.value = false;
};

const submit = () => form.post('/cabinet/parts/import', { forceFormData: true });
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-2xl">
            <div>
                <Link href="/cabinet/parts/products" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку товаров
                </Link>
                <h1 class="page-title">Импорт товаров</h1>
                <p class="page-subtitle">Загрузите прайс-лист в формате Excel (.xlsx) или CSV</p>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <label
                    class="block rounded-2xl border-2 border-dashed p-8 text-center cursor-pointer transition-colors"
                    :class="dragging ? 'border-primary-bright bg-primary-light' : 'border-outline hover:border-primary/40 hover:bg-surface-muted'"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="onFile"
                >
                    <input type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="onFile" />
                    <div v-if="!fileName" class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-xl bg-primary-light flex items-center justify-center">
                            <UploadCloud class="w-6 h-6 text-primary" />
                        </div>
                        <p class="text-sm font-semibold text-on-surface">Перетащите файл сюда или нажмите для выбора</p>
                        <p class="text-xs text-on-surface-muted">XLSX, XLS, CSV до 10 МБ</p>
                    </div>
                    <div v-else class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-xl bg-success-bg flex items-center justify-center">
                            <FileSpreadsheet class="w-6 h-6 text-success" />
                        </div>
                        <p class="text-sm font-semibold text-on-surface flex items-center gap-1.5">
                            <CheckCircle2 class="w-4 h-4 text-success" /> {{ fileName }}
                        </p>
                        <p class="text-xs text-on-surface-muted">Файл готов к загрузке</p>
                    </div>
                </label>

                <p v-if="form.errors.file" class="text-xs text-danger">{{ form.errors.file }}</p>

                <div class="rounded-xl bg-surface-muted p-4 text-xs text-on-surface-muted leading-relaxed">
                    <p class="font-semibold text-on-surface mb-1">Формат файла</p>
                    Колонки: <span class="font-mono">Название, Артикул, OEM, Бренд, Цена розн., Цена опт., Остаток</span>.
                    Первая строка — заголовки.
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing" :disabled="!form.file">Загрузить и импортировать</Button>
                    <Link href="/cabinet/parts/products" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
