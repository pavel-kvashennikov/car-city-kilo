<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ExternalLink, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.product.name,
    category_id: props.product.category_id ?? '',
    article_number: props.product.article_number ?? '',
    oem_number: props.product.oem_number ?? '',
    brand: props.product.brand ?? '',
    price_retail: props.product.price_retail,
    price_wholesale: props.product.price_wholesale ?? '',
    stock_quantity: props.product.stock_quantity,
    status: props.product.status ?? 'active',
    description: props.product.description ?? '',
});

const statuses = [
    { value: 'active', label: 'Активен' },
    { value: 'inactive', label: 'Неактивен' },
    { value: 'out_of_stock', label: 'Нет в наличии' },
    { value: 'archived', label: 'Архив' },
];

const submit = () => form.put(`/cabinet/parts/products/${props.product.id}`);
const destroy = () => {
    if (confirm('Удалить этот товар?')) {
        router.delete(`/cabinet/parts/products/${props.product.id}`);
    }
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-3xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/parts/products" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку товаров
                    </Link>
                    <h1 class="page-title">{{ product.name }}</h1>
                </div>
                <Link v-if="product.slug" :href="`/catalog/parts/${product.slug}`" target="_blank" class="btn-secondary !text-xs !py-1.5 shrink-0">
                    <ExternalLink class="w-3.5 h-3.5" /> На сайте
                </Link>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Название" :error="form.errors.name" required />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.article_number" label="Артикул" />
                    <Input v-model="form.oem_number" label="OEM-номер" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.brand" label="Бренд" />
                    <Select v-model="form.category_id" label="Категория" placeholder="— Выберите —" :options="categories" option-value="id" option-label="name" />
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <Input v-model="form.price_retail" label="Цена розн., ₽" type="number" />
                    <Input v-model="form.price_wholesale" label="Цена опт., ₽" type="number" />
                    <Input v-model="form.stock_quantity" label="Остаток, шт." type="number" />
                </div>
                <Select v-model="form.status" label="Статус" :options="statuses" />
                <Textarea v-model="form.description" label="Описание" :rows="3" />

                <div class="flex items-center justify-between pt-2 border-t border-outline">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/parts/products" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy" class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
