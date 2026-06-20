<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { ChevronLeft, ExternalLink, Trash2, Plus, X, Tag, Car } from 'lucide-vue-next';

const props = defineProps({
    product:    { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    brands:     { type: Array, default: () => [] },
});

const form = useForm({
    name:              props.product.name ?? '',
    category_id:       props.product.category_id ?? '',
    article_number:    props.product.article_number ?? '',
    oem_number:        props.product.oem_number ?? '',
    barcode:           props.product.barcode ?? '',
    brand:             props.product.brand ?? '',
    condition:         props.product.condition ?? 'new',
    part_type:         props.product.part_type ?? 'aftermarket',
    price_retail:      props.product.price_retail ?? '',
    price_wholesale:   props.product.price_wholesale ?? '',
    wholesale_min_qty: props.product.wholesale_min_qty ?? '',
    stock_quantity:    props.product.stock_quantity ?? '',
    description:       props.product.description ?? '',
    status:            props.product.status ?? 'active',
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
const statuses = [
    { value: 'active',       label: 'Активен' },
    { value: 'inactive',     label: 'Неактивен' },
    { value: 'out_of_stock', label: 'Нет в наличии' },
    { value: 'archived',     label: 'Архив' },
];

const submit  = () => form.put(`/cabinet/parts/products/${props.product.id}`);
const destroy = () => {
    if (confirm('Удалить этот товар? Это действие необратимо.')) {
        router.delete(`/cabinet/parts/products/${props.product.id}`);
    }
};

// ─── Cross-numbers ────────────────────────────────────────────────────────────
const crossForm = useForm({ number: '', brand: '', is_oem: false });

function addCross() {
    crossForm.post(
        `/cabinet/parts/products/${props.product.id}/cross-numbers`,
        { preserveScroll: true, onSuccess: () => crossForm.reset() }
    );
}
function deleteCross(id) {
    router.delete(`/cabinet/parts/products/${props.product.id}/cross-numbers/${id}`,
        { preserveScroll: true });
}

// ─── Applicability ────────────────────────────────────────────────────────────
const applForm = useForm({ brand_id: '', model_id: '', year_from: '', year_to: '' });

const applModels = computed(() => {
    if (!applForm.brand_id) return [];
    return props.brands.find(b => b.id == applForm.brand_id)?.models ?? [];
});
watch(() => applForm.brand_id, () => { applForm.model_id = ''; });

function addAppl() {
    applForm.post(
        `/cabinet/parts/products/${props.product.id}/applicabilities`,
        { preserveScroll: true, onSuccess: () => applForm.reset() }
    );
}
function deleteAppl(id) {
    router.delete(`/cabinet/parts/products/${props.product.id}/applicabilities/${id}`,
        { preserveScroll: true });
}
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6 max-w-3xl">

            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/parts/products"
                        class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку товаров
                    </Link>
                    <h1 class="page-title">{{ product.name }}</h1>
                    <p v-if="product.category?.name" class="text-sm text-on-surface-muted mt-0.5">
                        {{ product.category.name }}
                    </p>
                </div>
                <Link v-if="product.slug" :href="`/catalog/parts/${product.slug}`" target="_blank"
                    class="btn-secondary !text-xs !py-1.5 shrink-0">
                    <ExternalLink class="w-3.5 h-3.5" /> На сайте
                </Link>
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input v-model="form.brand" label="Производитель / бренд" :error="form.errors.brand" />
                        <Select v-model="form.status" label="Статус" :options="statuses" :error="form.errors.status" />
                    </div>
                </div>

                <!-- Артикулы и идентификаторы -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Артикулы и коды</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input v-model="form.article_number" label="Артикул продавца" :error="form.errors.article_number" />
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

                <!-- Кросс-номера -->
                <div class="card p-5 space-y-4">
                    <div class="flex items-center gap-2 border-b border-outline pb-3">
                        <Tag class="w-4 h-4 text-primary" />
                        <h2 class="font-semibold text-on-surface">Кросс-номера (аналоги)</h2>
                        <span class="text-xs text-on-surface-muted ml-auto">Используются для поиска по OEM и номерам аналогов</span>
                    </div>

                    <!-- Existing -->
                    <div v-if="product.cross_numbers?.length" class="flex flex-wrap gap-2">
                        <div v-for="cn in product.cross_numbers" :key="cn.id"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-sm"
                            :class="cn.is_oem ? 'border-primary bg-primary-light text-primary' : 'border-outline bg-surface-muted text-on-surface'">
                            <span class="font-mono font-medium">{{ cn.number }}</span>
                            <span v-if="cn.brand" class="text-xs opacity-70">{{ cn.brand }}</span>
                            <span v-if="cn.is_oem" class="text-xs font-bold uppercase">OEM</span>
                            <button @click="deleteCross(cn.id)"
                                class="ml-1 text-on-surface-muted hover:text-danger transition-colors">
                                <X class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                    <p v-else class="text-sm text-on-surface-muted italic">Кросс-номера не добавлены</p>

                    <!-- Add form -->
                    <div class="flex flex-wrap items-end gap-3 pt-2 border-t border-outline">
                        <div class="flex-1 min-w-36">
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Номер <span class="text-danger">*</span>
                            </label>
                            <input v-model="crossForm.number" placeholder="Напр. HU610x"
                                class="input-field" @keydown.enter.prevent="addCross" />
                            <p v-if="crossForm.errors.number" class="text-xs text-danger mt-1">{{ crossForm.errors.number }}</p>
                        </div>
                        <div class="flex-1 min-w-28">
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Бренд</label>
                            <input v-model="crossForm.brand" placeholder="Mahle, Mann..."
                                class="input-field" />
                        </div>
                        <div class="flex items-center gap-2 pb-0.5">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface">
                                <input type="checkbox" v-model="crossForm.is_oem" class="w-4 h-4 accent-primary" />
                                OEM-номер
                            </label>
                        </div>
                        <button type="button" @click="addCross" :disabled="!crossForm.number || crossForm.processing"
                            class="btn-primary !text-sm !py-2 disabled:opacity-50">
                            <Plus class="w-4 h-4" /> Добавить
                        </button>
                    </div>
                </div>

                <!-- Применяемость -->
                <div class="card p-5 space-y-4">
                    <div class="flex items-center gap-2 border-b border-outline pb-3">
                        <Car class="w-4 h-4 text-primary" />
                        <h2 class="font-semibold text-on-surface">Применяемость</h2>
                        <span class="text-xs text-on-surface-muted ml-auto">Для каких автомобилей подходит эта деталь</span>
                    </div>

                    <!-- Existing -->
                    <div v-if="product.applicabilities?.length" class="space-y-2">
                        <div v-for="a in product.applicabilities" :key="a.id"
                            class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg bg-surface-muted text-sm">
                            <div class="flex items-center gap-2">
                                <Car class="w-3.5 h-3.5 text-on-surface-muted shrink-0" />
                                <span class="font-medium text-on-surface">{{ a.brand?.name }}</span>
                                <span v-if="a.car_model?.name" class="text-on-surface-muted">{{ a.car_model.name }}</span>
                                <span v-if="a.year_from || a.year_to" class="text-on-surface-muted">
                                    ({{ a.year_from ?? '…' }} – {{ a.year_to ?? 'н.в.' }})
                                </span>
                            </div>
                            <button @click="deleteAppl(a.id)"
                                class="text-on-surface-muted hover:text-danger transition-colors shrink-0">
                                <X class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                    <p v-else class="text-sm text-on-surface-muted italic">Применяемость не указана</p>

                    <!-- Add form -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-outline items-end">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Марка <span class="text-danger">*</span>
                            </label>
                            <select v-model="applForm.brand_id" class="input-field">
                                <option value="">— Марка —</option>
                                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                            <p v-if="applForm.errors.brand_id" class="text-xs text-danger mt-1">{{ applForm.errors.brand_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Модель</label>
                            <select v-model="applForm.model_id" class="input-field disabled:bg-surface-muted"
                                :disabled="!applModels.length">
                                <option value="">— Любая —</option>
                                <option v-for="m in applModels" :key="m.id" :value="m.id">{{ m.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Год от</label>
                            <input v-model="applForm.year_from" type="number" min="1900" :max="new Date().getFullYear() + 1"
                                placeholder="2010" class="input-field" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Год до</label>
                            <input v-model="applForm.year_to" type="number" min="1900" :max="new Date().getFullYear() + 1"
                                placeholder="2020" class="input-field" />
                        </div>
                        <div class="col-span-2 sm:col-span-4 flex justify-end">
                            <button type="button" @click="addAppl" :disabled="!applForm.brand_id || applForm.processing"
                                class="btn-primary !text-sm !py-2 disabled:opacity-50">
                                <Plus class="w-4 h-4" /> Добавить применяемость
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Описание</h2>
                    <Textarea v-model="form.description" :rows="4"
                        placeholder="Дополнительная информация о товаре: совместимость, особенности, комплектность..."
                        :error="form.errors.description" />
                </div>

                <!-- Кнопки -->
                <div class="card p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить изменения</Button>
                        <Link href="/cabinet/parts/products" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy"
                        class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить товар
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
