<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ExternalLink, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    vehicle: { type: Object, required: true },
    brands: { type: Array, default: () => [] },
});

const form = useForm({
    brand_id: props.vehicle.brand_id,
    model_id: props.vehicle.model_id,
    year: props.vehicle.year,
    vin: props.vehicle.vin ?? '',
    mileage: props.vehicle.mileage,
    price: props.vehicle.price,
    color: props.vehicle.color ?? '',
    transmission: props.vehicle.transmission ?? '',
    condition: props.vehicle.condition ?? 'used',
    description: props.vehicle.description ?? '',
    status: props.vehicle.status ?? 'draft',
});

const transmissions = [
    { value: 'manual', label: 'Механика' },
    { value: 'automatic', label: 'Автомат' },
    { value: 'robot', label: 'Робот' },
    { value: 'cvt', label: 'Вариатор' },
];
const conditions = [
    { value: 'new', label: 'Новый' },
    { value: 'used', label: 'С пробегом' },
];
const statuses = [
    { value: 'draft', label: 'Черновик' },
    { value: 'pending', label: 'На модерации' },
    { value: 'active', label: 'Активно' },
    { value: 'sold', label: 'Продано' },
    { value: 'archived', label: 'Архив' },
];

const submit = () => form.put(`/cabinet/dealer/vehicles/${props.vehicle.id}`);

const destroy = () => {
    if (confirm('Удалить это объявление?')) {
        router.delete(`/cabinet/dealer/vehicles/${props.vehicle.id}`);
    }
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-3xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/dealer/vehicles" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку автомобилей
                    </Link>
                    <h1 class="page-title">{{ vehicle.brand?.name }} {{ vehicle.car_model?.name }}</h1>
                </div>
                <Link v-if="vehicle.slug" :href="`/catalog/cars/${vehicle.slug}`" target="_blank" class="btn-secondary !text-xs !py-1.5 shrink-0">
                    <ExternalLink class="w-3.5 h-3.5" /> На сайте
                </Link>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Select v-model="form.brand_id" label="Марка" :options="brands" option-value="id" option-label="name" />
                    <Input v-model="form.year" label="Год" type="number" />
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <Input v-model="form.mileage" label="Пробег, км" type="number" />
                    <Input v-model="form.price" label="Цена, ₽" type="number" />
                    <Input v-model="form.color" label="Цвет" />
                </div>
                <Input v-model="form.vin" label="VIN" />
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <Select v-model="form.transmission" label="КПП" placeholder="—" :options="transmissions" />
                    <Select v-model="form.condition" label="Состояние" :options="conditions" />
                    <Select v-model="form.status" label="Статус" :options="statuses" />
                </div>
                <Textarea v-model="form.description" label="Описание" :rows="4" />

                <div class="flex items-center justify-between pt-2 border-t border-outline">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/dealer/vehicles" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy" class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
