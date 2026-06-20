<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import {
    ChevronLeft, ExternalLink, Trash2, Upload, X, Star, ImageOff, CheckSquare
} from 'lucide-vue-next';

const props = defineProps({
    vehicle: { type: Object, required: true },
    brands:  { type: Array, default: () => [] },
});

// ─── Cascading selects ────────────────────────────────────────────────────────
const selectedBrand = ref(props.vehicle.brand_id ?? null);
const selectedModel = ref(props.vehicle.model_id ?? null);

const models = computed(() => {
    if (!selectedBrand.value) return [];
    return props.brands.find(b => b.id == selectedBrand.value)?.models ?? [];
});

const generations = computed(() => {
    if (!selectedModel.value) return [];
    return models.value.find(m => m.id == selectedModel.value)?.generations ?? [];
});

watch(selectedBrand, () => {
    selectedModel.value = null;
    form.brand_id = selectedBrand.value;
    form.model_id = null;
    form.generation_id = null;
});
watch(selectedModel, () => {
    form.model_id = selectedModel.value;
    form.generation_id = null;
});

// ─── Form ─────────────────────────────────────────────────────────────────────
const form = useForm({
    brand_id:       props.vehicle.brand_id ?? null,
    model_id:       props.vehicle.model_id ?? null,
    generation_id:  props.vehicle.generation_id ?? null,
    year:           props.vehicle.year ?? '',
    vin:            props.vehicle.vin ?? '',
    mileage:        props.vehicle.mileage ?? '',
    color:          props.vehicle.color ?? '',
    engine_type:    props.vehicle.engine_type ?? '',
    engine_volume:  props.vehicle.engine_volume ?? '',
    engine_power:   props.vehicle.engine_power ?? '',
    transmission:   props.vehicle.transmission ?? '',
    drive_type:     props.vehicle.drive_type ?? '',
    body_type:      props.vehicle.body_type ?? '',
    condition:      props.vehicle.condition ?? 'used',
    price:          props.vehicle.price ?? '',
    price_trade_in: props.vehicle.price_trade_in ?? '',
    description:    props.vehicle.description ?? '',
    features:       props.vehicle.features ?? [],
    status:         props.vehicle.status?.value ?? props.vehicle.status ?? 'draft',
});

// ─── Options ──────────────────────────────────────────────────────────────────
const transmissions = [
    { value: 'manual',    label: 'Механика' },
    { value: 'automatic', label: 'Автомат' },
    { value: 'robot',     label: 'Робот' },
    { value: 'cvt',       label: 'Вариатор' },
];
const engineTypes = [
    { value: 'petrol',   label: 'Бензин' },
    { value: 'diesel',   label: 'Дизель' },
    { value: 'hybrid',   label: 'Гибрид' },
    { value: 'electric', label: 'Электро' },
    { value: 'gas',      label: 'Газ' },
];
const driveTypes = [
    { value: 'fwd', label: 'Передний' },
    { value: 'rwd', label: 'Задний' },
    { value: 'awd', label: 'Полный' },
];
const bodyTypes = [
    { value: 'sedan',     label: 'Седан' },
    { value: 'hatchback', label: 'Хэтчбек' },
    { value: 'suv',       label: 'Внедорожник' },
    { value: 'crossover', label: 'Кроссовер' },
    { value: 'wagon',     label: 'Универсал' },
    { value: 'coupe',     label: 'Купе' },
    { value: 'minivan',   label: 'Минивэн' },
    { value: 'pickup',    label: 'Пикап' },
];
const conditions = [
    { value: 'new',     label: 'Новый' },
    { value: 'used',    label: 'С пробегом' },
    { value: 'damaged', label: 'После ДТП' },
];
const statuses = [
    { value: 'draft',    label: 'Черновик' },
    { value: 'pending',  label: 'На модерации' },
    { value: 'active',   label: 'Активно' },
    { value: 'sold',     label: 'Продано' },
    { value: 'archived', label: 'Архив' },
];
const featuresList = [
    'Кондиционер', 'Климат-контроль', 'Подогрев сидений', 'Подогрев руля',
    'Навигация', 'Камера заднего вида', 'Парктроник', 'Круиз-контроль',
    'Кожаный салон', 'Люк', 'Электростёкла', 'Центральный замок',
    'ABS', 'ESP', 'Подушки безопасности', 'LED фары',
];

const toggleFeature = (feat) => {
    const idx = form.features.indexOf(feat);
    if (idx === -1) form.features.push(feat);
    else form.features.splice(idx, 1);
};

// ─── Submit ───────────────────────────────────────────────────────────────────
const submit = () => form.put(`/cabinet/dealer/vehicles/${props.vehicle.id}`);

const destroy = () => {
    if (confirm('Удалить это объявление? Это действие необратимо.')) {
        router.delete(`/cabinet/dealer/vehicles/${props.vehicle.id}`);
    }
};

// ─── Photos ───────────────────────────────────────────────────────────────────
const photoInput = ref(null);
const uploadingPhotos = ref(false);

function onFilesSelected(e) {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    uploadingPhotos.value = true;
    const fd = new FormData();
    files.forEach(f => fd.append('photos[]', f));

    router.post(
        `/cabinet/dealer/vehicles/${props.vehicle.id}/photos`,
        fd,
        {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => {
                uploadingPhotos.value = false;
                if (photoInput.value) photoInput.value.value = '';
            },
        }
    );
}

function deletePhoto(photo) {
    if (!confirm('Удалить фото?')) return;
    router.delete(
        `/cabinet/dealer/vehicles/${props.vehicle.id}/photos/${photo.id}`,
        { preserveScroll: true }
    );
}

function setMainPhoto(photo) {
    router.put(
        `/cabinet/dealer/vehicles/${props.vehicle.id}/photos/${photo.id}/main`,
        {},
        { preserveScroll: true }
    );
}

function photoUrl(path) {
    return path ? `/storage/${path}` : null;
}

const statusBadge = computed(() => {
    const map = {
        draft:    'badge-neutral',
        pending:  'badge-warning',
        active:   'badge-success',
        sold:     'badge-primary',
        archived: 'badge-neutral',
    };
    return map[form.status] ?? 'badge-neutral';
});
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6 max-w-4xl">

            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/dealer/vehicles"
                        class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку автомобилей
                    </Link>
                    <h1 class="page-title">
                        {{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}
                        <span v-if="vehicle.year" class="text-on-surface-muted font-normal text-xl">({{ vehicle.year }})</span>
                    </h1>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="statusBadge">
                        {{ statuses.find(s => s.value === form.status)?.label ?? form.status }}
                    </span>
                    <Link v-if="vehicle.slug" :href="`/catalog/cars/${vehicle.slug}`" target="_blank"
                        class="btn-secondary !text-xs !py-1.5">
                        <ExternalLink class="w-3.5 h-3.5" /> На сайте
                    </Link>
                </div>
            </div>

            <!-- Photos section -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-on-surface">Фотографии</h2>
                    <label class="btn-secondary !text-xs !py-1.5 cursor-pointer" :class="{ 'opacity-60': uploadingPhotos }">
                        <Upload class="w-3.5 h-3.5" />
                        {{ uploadingPhotos ? 'Загрузка...' : 'Добавить фото' }}
                        <input ref="photoInput" type="file" accept="image/*" multiple class="sr-only"
                            :disabled="uploadingPhotos" @change="onFilesSelected" />
                    </label>
                </div>

                <div v-if="vehicle.photos?.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div v-for="photo in vehicle.photos" :key="photo.id"
                        class="relative group rounded-xl overflow-hidden aspect-[4/3] bg-surface-muted">
                        <img v-if="photoUrl(photo.path)" :src="photoUrl(photo.path)"
                            class="w-full h-full object-cover" :alt="'Фото авто'" />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <ImageOff class="w-8 h-8 text-outline" />
                        </div>

                        <!-- Main badge -->
                        <div v-if="photo.is_main"
                            class="absolute top-1.5 left-1.5 bg-accent text-white text-xs font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                            <Star class="w-3 h-3" /> Главное
                        </div>

                        <!-- Actions overlay -->
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button v-if="!photo.is_main" @click="setMainPhoto(photo)"
                                class="w-8 h-8 rounded-full bg-white/90 hover:bg-white flex items-center justify-center text-amber-500"
                                title="Сделать главным">
                                <Star class="w-4 h-4" />
                            </button>
                            <button @click="deletePhoto(photo)"
                                class="w-8 h-8 rounded-full bg-white/90 hover:bg-white flex items-center justify-center text-danger"
                                title="Удалить">
                                <X class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="border-2 border-dashed border-outline rounded-xl p-10 text-center">
                    <ImageOff class="w-10 h-10 text-outline mx-auto mb-2" />
                    <p class="text-sm text-on-surface-muted">Нет фотографий. Добавьте хотя бы одно фото.</p>
                </div>
            </div>

            <!-- Main form -->
            <form class="space-y-5" @submit.prevent="submit">

                <!-- Basic info -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Основные данные</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Марка</label>
                            <select v-model="selectedBrand" class="input-field">
                                <option value="">— Выберите марку —</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.brand_id" class="text-xs text-danger mt-1.5">{{ form.errors.brand_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Модель</label>
                            <select v-model="selectedModel" class="input-field disabled:bg-surface-muted disabled:cursor-not-allowed" :disabled="!models.length">
                                <option value="">— Выберите модель —</option>
                                <option v-for="model in models" :key="model.id" :value="model.id">
                                    {{ model.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.model_id" class="text-xs text-danger mt-1.5">{{ form.errors.model_id }}</p>
                        </div>
                    </div>

                    <div v-if="generations.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Поколение</label>
                            <select v-model="form.generation_id" class="input-field">
                                <option :value="null">— Не указано —</option>
                                <option v-for="gen in generations" :key="gen.id" :value="gen.id">
                                    {{ gen.name }}
                                    <template v-if="gen.year_from"> ({{ gen.year_from }}
                                        <template v-if="gen.year_to">–{{ gen.year_to }}</template>
                                        <template v-else>–н.в.</template>)
                                    </template>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <Input v-model="form.year" label="Год" type="number" min="1900" :max="new Date().getFullYear() + 1"
                            :error="form.errors.year" required />
                        <Input v-model="form.mileage" label="Пробег, км" type="number" min="0"
                            :error="form.errors.mileage" required />
                        <Input v-model="form.price" label="Цена, ₽" type="number" min="0"
                            :error="form.errors.price" required />
                        <Input v-model="form.price_trade_in" label="Trade-in цена, ₽" type="number" min="0"
                            :error="form.errors.price_trade_in" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input v-model="form.vin" label="VIN (17 символов)" maxlength="17"
                            :error="form.errors.vin" />
                        <Input v-model="form.color" label="Цвет" :error="form.errors.color" />
                    </div>
                </div>

                <!-- Technical specs -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Технические характеристики</h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <Select v-model="form.engine_type" label="Тип двигателя" placeholder="—" :options="engineTypes" />
                        <Input v-model="form.engine_volume" label="Объём, л" type="number" step="0.1" min="0"
                            :error="form.errors.engine_volume" />
                        <Input v-model="form.engine_power" label="Мощность, л.с." type="number" min="0"
                            :error="form.errors.engine_power" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <Select v-model="form.transmission" label="КПП" placeholder="—" :options="transmissions" />
                        <Select v-model="form.drive_type" label="Привод" placeholder="—" :options="driveTypes" />
                        <Select v-model="form.body_type" label="Тип кузова" placeholder="—" :options="bodyTypes" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Select v-model="form.condition" label="Состояние" :options="conditions"
                            :error="form.errors.condition" />
                        <Select v-model="form.status" label="Статус объявления" :options="statuses"
                            :error="form.errors.status" />
                    </div>
                </div>

                <!-- Features -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Комплектация и опции</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        <label
                            v-for="feat in featuresList"
                            :key="feat"
                            class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border transition-colors"
                            :class="form.features.includes(feat)
                                ? 'border-primary bg-primary-light text-primary'
                                : 'border-outline hover:border-primary/50'"
                        >
                            <input type="checkbox"
                                :checked="form.features.includes(feat)"
                                @change="toggleFeature(feat)"
                                class="sr-only" />
                            <CheckSquare v-if="form.features.includes(feat)" class="w-4 h-4 shrink-0" />
                            <span v-else class="w-4 h-4 shrink-0 rounded border border-outline" />
                            <span class="text-sm">{{ feat }}</span>
                        </label>
                    </div>
                </div>

                <!-- Description -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Описание</h2>
                    <Textarea v-model="form.description" :rows="5"
                        placeholder="Состояние кузова и салона, история обслуживания, причина продажи..."
                        :error="form.errors.description" />
                </div>

                <!-- Actions -->
                <div class="card p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить изменения</Button>
                        <Link href="/cabinet/dealer/vehicles" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy"
                        class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить объявление
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
