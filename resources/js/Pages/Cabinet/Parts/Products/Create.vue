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
    name:              '',
    category_id:       '',
    article_number:    '',
    oem_number:        '',
    barcode:           '',
    brand:             '',
    condition:         'new',
    part_type:         'aftermarket',
    price_retail:      '',
    price_wholesale:   '',
    wholesale_min_qty: '',
    stock_quantity:    '',
    description:       '',
});

const conditions = [
    { value: 'new',         label: 'Новая' },
    { value: 'used',        label: 'Б/У' },
    { value: 'refurbished', label: 'Восстановленная' },
];
const partTypes = [
    { value: 'original',    label: 'Оригинал' },
    { value: 'oem',         label: 'OEM' },
    { value: 'aftermarket', label: 'Аналог' },
];

const submit = () => form.post('/cabinet/parts/products');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6 max-w-3xl">

            <!-- Header -->
            <div>
                <Link href="/cabinet/parts/products"
                    class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку товаров
                </Link>
                <h1 class="page-title">Добавить товар</h1>
            </div>

            <form class="space-y-5" @submit.prevent="submit">

                <!-- Основные данные -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Основные данные</h2>

                    <Input v-model="form.name" label="Название" :error="form.errors.name" required />

                    <Select v-model="form.category_id" label="Категория" placeholder="— Выберите категорию —"
                        :options="categories" option-value="id" option-label="name" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Select v-model="form.part_type" label="Тип запчасти" :options="partTypes"
                            :error="form.errors.part_type" />
                        <Select v-model="form.condition" label="Состояние" :options="conditions"
                            :error="form.errors.condition" />
                    </div>

                    <Input v-model="form.brand" label="Производитель / бренд" :error="form.errors.brand" />
                </div>

                <!-- Артикулы и идентификаторы -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Артикулы и коды</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input v-model="form.article_number" label="Артикул продавца"
                            :error="form.errors.article_number" />
                        <Input v-model="form.oem_number" label="OEM-номер" :error="form.errors.oem_number" />
                    </div>
                    <Input v-model="form.barcode" label="Штрихкод (EAN/UPC)" :error="form.errors.barcode" />
                </div>

                <!-- Цены и склад -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Цены и склад</h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <Input v-model="form.price_retail" label="Розн. цена, ₽" type="number" min="0"
                            :error="form.errors.price_retail" required />
                        <Input v-model="form.price_wholesale" label="Опт. цена, ₽" type="number" min="0"
                            :error="form.errors.price_wholesale" />
                        <Input v-model="form.wholesale_min_qty" label="Мин. опт. кол-во" type="number" min="1"
                            :error="form.errors.wholesale_min_qty" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input v-model="form.stock_quantity" label="Остаток на складе, шт." type="number" min="0"
                            :error="form.errors.stock_quantity" required />
                    </div>
                </div>

                <!-- Описание -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Описание</h2>
                    <Textarea v-model="form.description" :rows="4"
                        placeholder="Дополнительная информация: совместимость, особенности, комплектность..."
                        :error="form.errors.description" />
                </div>

                <!-- Кнопки -->
                <div class="card p-4 flex items-center gap-3">
                    <Button type="submit" :loading="form.processing">Создать товар</Button>
                    <Link href="/cabinet/parts/products" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
