<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';

defineProps({
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    article_number: '',
    oem_number: '',
    brand: '',
    category_id: '',
    price_retail: '',
    price_wholesale: '',
    stock_quantity: '',
    condition: 'new',
    part_type: 'aftermarket',
    description: '',
});

const conditions = [
    { value: 'new', label: 'Новая' },
    { value: 'used', label: 'Б/У' },
];
const partTypes = [
    { value: 'original', label: 'Оригинал' },
    { value: 'aftermarket', label: 'Аналог' },
];

const submit = () => form.post('/cabinet/parts/products');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-3xl">
            <div>
                <Link href="/cabinet/parts/products" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку товаров
                </Link>
                <h1 class="page-title">Добавить товар</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Название" :error="form.errors.name" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.article_number" label="Артикул" :error="form.errors.article_number" />
                    <Input v-model="form.oem_number" label="OEM-номер" :error="form.errors.oem_number" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.brand" label="Бренд" :error="form.errors.brand" />
                    <Select v-model="form.category_id" label="Категория" placeholder="— Выберите —" :options="categories" option-value="id" option-label="name" />
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <Input v-model="form.price_retail" label="Цена розн., ₽" type="number" :error="form.errors.price_retail" />
                    <Input v-model="form.price_wholesale" label="Цена опт., ₽" type="number" />
                    <Input v-model="form.stock_quantity" label="Остаток, шт." type="number" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <Select v-model="form.condition" label="Состояние" :options="conditions" />
                    <Select v-model="form.part_type" label="Тип" :options="partTypes" />
                </div>

                <Textarea v-model="form.description" label="Описание" :rows="3" />

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Создать товар</Button>
                    <Link href="/cabinet/parts/products" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
