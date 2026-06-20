<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';

defineProps({
    brands: { type: Array, default: () => [] },
    models: { type: Array, default: () => [] },
});

const form = useForm({
    brand_id: '',
    model_id: '',
    year: '',
    vin: '',
    mileage: '',
    price: '',
    color: '',
    engine_type: '',
    engine_volume: '',
    engine_power: '',
    transmission: '',
    drive_type: '',
    body_type: '',
    condition: 'used',
    description: '',
});

const transmissions = [
    { value: 'manual', label: 'Механика' },
    { value: 'automatic', label: 'Автомат' },
    { value: 'robot', label: 'Робот' },
    { value: 'cvt', label: 'Вариатор' },
];
const engineTypes = [
    { value: 'petrol', label: 'Бензин' },
    { value: 'diesel', label: 'Дизель' },
    { value: 'hybrid', label: 'Гибрид' },
    { value: 'electric', label: 'Электро' },
    { value: 'gas', label: 'Газ' },
];
const driveTypes = [
    { value: 'fwd', label: 'Передний' },
    { value: 'rwd', label: 'Задний' },
    { value: 'awd', label: 'Полный' },
];
const bodyTypes = [
    { value: 'sedan', label: 'Седан' },
    { value: 'hatchback', label: 'Хэтчбек' },
    { value: 'suv', label: 'Внедорожник' },
    { value: 'crossover', label: 'Кроссовер' },
    { value: 'wagon', label: 'Универсал' },
    { value: 'coupe', label: 'Купе' },
    { value: 'minivan', label: 'Минивэн' },
    { value: 'pickup', label: 'Пикап' },
];
const conditions = [
    { value: 'new', label: 'Новый' },
    { value: 'used', label: 'С пробегом' },
];

const submit = () => form.post('/cabinet/dealer/vehicles');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-3xl">
            <div>
                <Link href="/cabinet/dealer/vehicles" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку автомобилей
                </Link>
                <h1 class="page-title">Добавить автомобиль</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Select v-model="form.brand_id" label="Марка" placeholder="Выберите марку" :options="brands" option-value="id" option-label="name" :error="form.errors.brand_id" required />
                    <Select v-model="form.model_id" label="Модель" placeholder="Выберите модель" :options="models" option-value="id" option-label="name" :error="form.errors.model_id" required />
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <Input v-model="form.year" label="Год" type="number" :error="form.errors.year" required />
                    <Input v-model="form.mileage" label="Пробег, км" type="number" :error="form.errors.mileage" required />
                    <Input v-model="form.price" label="Цена, ₽" type="number" :error="form.errors.price" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.vin" label="VIN" :error="form.errors.vin" />
                    <Input v-model="form.color" label="Цвет" :error="form.errors.color" />
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <Select v-model="form.engine_type" label="Тип двигателя" placeholder="—" :options="engineTypes" />
                    <Input v-model="form.engine_volume" label="Объём, л" type="number" step="0.1" />
                    <Input v-model="form.engine_power" label="Мощность, л.с." type="number" />
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <Select v-model="form.transmission" label="КПП" placeholder="—" :options="transmissions" />
                    <Select v-model="form.drive_type" label="Привод" placeholder="—" :options="driveTypes" />
                    <Select v-model="form.body_type" label="Кузов" placeholder="—" :options="bodyTypes" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Select v-model="form.condition" label="Состояние" :options="conditions" />
                </div>

                <Textarea v-model="form.description" label="Описание" :rows="4" placeholder="Состояние, комплектация, история обслуживания..." :error="form.errors.description" />

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Создать объявление</Button>
                    <Link href="/cabinet/dealer/vehicles" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
