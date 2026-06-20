<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    item:       { type: Object, required: true },
    categories: { type: Array, default: () => [] },
});

const vehicleTypeOptions = [
    { value: 'car',      label: 'Легковой' },
    { value: 'suv',      label: 'Внедорожник / кроссовер' },
    { value: 'van',      label: 'Минивэн / фургон' },
    { value: 'truck',    label: 'Грузовой' },
    { value: 'moto',     label: 'Мотоцикл' },
    { value: 'electric', label: 'Электромобиль' },
];

const form = useForm({
    name:             props.item.name ?? '',
    category_id:      props.item.category_id ?? '',
    description:      props.item.description ?? '',
    price_fixed:      props.item.price_fixed ?? '',
    price_from:       props.item.price_from ?? '',
    price_to:         props.item.price_to ?? '',
    price_per_hour:   props.item.price_per_hour ?? '',
    duration_minutes: props.item.duration_minutes ?? 60,
    vehicle_types:    props.item.vehicle_types ?? [],
    is_active:        props.item.is_active ?? true,
    sort_order:       props.item.sort_order ?? 0,
});

const toggleVehicleType = (val) => {
    const idx = form.vehicle_types.indexOf(val);
    if (idx === -1) form.vehicle_types.push(val);
    else form.vehicle_types.splice(idx, 1);
};

const submit = () => form.put(`/cabinet/service/items/${props.item.id}`);

const destroy = () => {
    if (confirm('Удалить эту услугу? Это действие необратимо.'))
        router.delete(`/cabinet/service/items/${props.item.id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6 max-w-2xl">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/service/items"
                        class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку услуг
                    </Link>
                    <h1 class="page-title">{{ item.name }}</h1>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                    :class="item.is_active ? 'badge-success' : 'badge-neutral'">
                    {{ item.is_active ? 'Активна' : 'Неактивна' }}
                </span>
            </div>

            <form class="space-y-5" @submit.prevent="submit">

                <!-- Основная информация -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Основная информация</h2>

                    <Input v-model="form.name" label="Название услуги" :error="form.errors.name" required />

                    <Select v-model="form.category_id" label="Категория" placeholder="— Не выбрана —"
                        :options="categories" option-value="id" option-label="name"
                        :error="form.errors.category_id" />

                    <Textarea v-model="form.description" label="Описание" :rows="3"
                        placeholder="Подробное описание, что включено..."
                        :error="form.errors.description" />
                </div>

                <!-- Стоимость -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Стоимость</h2>
                    <p class="text-xs text-on-surface-muted -mt-2">Укажите либо фиксированную цену, либо диапазон, либо нормо-час</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Input v-model="form.price_fixed" label="Фикс. цена, ₽" type="number" min="0"
                            :error="form.errors.price_fixed" />
                        <Input v-model="form.price_from" label="Цена от, ₽" type="number" min="0"
                            :error="form.errors.price_from" />
                        <Input v-model="form.price_to" label="Цена до, ₽" type="number" min="0"
                            :error="form.errors.price_to" />
                    </div>

                    <Input v-model="form.price_per_hour" label="Нормо-час, ₽/час" type="number" min="0"
                        :error="form.errors.price_per_hour"
                        placeholder="Стоимость 1 часа работы мастера" />
                </div>

                <!-- Параметры -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Параметры</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <Input v-model="form.duration_minutes" label="Длительность, мин" type="number" min="0"
                            :error="form.errors.duration_minutes" />
                        <Input v-model="form.sort_order" label="Порядок сортировки" type="number" min="0" />
                    </div>

                    <!-- Vehicle types -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-2 uppercase tracking-wide">
                            Типы ТС (пусто = все)
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <label v-for="opt in vehicleTypeOptions" :key="opt.value"
                                class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border text-sm transition-colors"
                                :class="form.vehicle_types.includes(opt.value)
                                    ? 'border-primary bg-primary-light text-primary'
                                    : 'border-outline hover:border-primary/50'">
                                <input type="checkbox" :checked="form.vehicle_types.includes(opt.value)"
                                    @change="toggleVehicleType(opt.value)" class="sr-only" />
                                {{ opt.label }}
                            </label>
                        </div>
                    </div>

                    <!-- Is active -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="w-4 h-4 accent-primary" />
                        <span class="text-sm font-medium text-on-surface">Услуга активна (видна в каталоге)</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="card p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/service/items" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy"
                        class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить услугу
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
