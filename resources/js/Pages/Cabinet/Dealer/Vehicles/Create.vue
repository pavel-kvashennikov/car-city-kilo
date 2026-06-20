<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { ChevronLeft, CheckSquare } from 'lucide-vue-next';

const props = defineProps({
    brands: { type: Array, default: () => [] },
});

// ─── Cascading selects ────────────────────────────────────────────────────────
const selectedBrand = ref(null);
const selectedModel = ref(null);

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
    brand_id:      null,
    model_id:      null,
    generation_id: null,
    year:          '',
    vin:           '',
    mileage:       '',
    color:         '',
    engine_type:   '',
    engine_volume: '',
    engine_power:  '',
    transmission:  '',
    drive_type:    '',
    body_type:     '',
    condition:     'used',
    price:         '',
    price_trade_in: '',
    description:   '',
    features:      [],
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

const submit = () => form.post('/cabinet/dealer/vehicles');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6 max-w-4xl">

            <!-- Header -->
            <div>
                <Link href="/cabinet/dealer/vehicles"
                    class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку автомобилей
                </Link>
                <h1 class="page-title">Добавить автомобиль</h1>
                <p class="text-sm text-on-surface-muted mt-1">После создания вы сможете добавить фотографии и отправить объявление на модерацию.</p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">

                <!-- Basic info -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Основные данные</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Марка <span class="text-danger">*</span>
                            </label>
                            <select v-model="selectedBrand" class="input-field" required>
                                <option value="">— Выберите марку —</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.brand_id" class="text-xs text-danger mt-1.5">{{ form.errors.brand_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Модель <span class="text-danger">*</span>
                            </label>
                            <select v-model="selectedModel" class="input-field disabled:bg-surface-muted disabled:cursor-not-allowed"
                                :disabled="!models.length" required>
                                <option value="">{{ models.length ? '— Выберите модель —' : '— Сначала выберите марку —' }}</option>
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
                                    <template v-if="gen.year_from">
                                        ({{ gen.year_from }}<template v-if="gen.year_to">–{{ gen.year_to }}</template><template v-else>–н.в.</template>)
                                    </template>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <Input v-model="form.year" label="Год" type="number" min="1900"
                            :max="new Date().getFullYear() + 1" :error="form.errors.year" required />
                        <Input v-model="form.mileage" label="Пробег, км" type="number" min="0"
                            :error="form.errors.mileage" required />
                        <Input v-model="form.price" label="Цена, ₽" type="number" min="0"
                            :error="form.errors.price" required />
                        <Input v-model="form.price_trade_in" label="Trade-in цена, ₽" type="number" min="0" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input v-model="form.vin" label="VIN (17 символов)" maxlength="17" :error="form.errors.vin" />
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
                            <span v-else class="w-4 h-4 shrink-0 rounded border border-outline inline-block" />
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
                <div class="card p-4 flex items-center gap-3">
                    <Button type="submit" :loading="form.processing">Создать объявление</Button>
                    <Link href="/cabinet/dealer/vehicles" class="btn-secondary !text-sm">Отмена</Link>
                    <p class="text-xs text-on-surface-muted ml-auto">
                        Фотографии можно добавить после создания
                    </p>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
