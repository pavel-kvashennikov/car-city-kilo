<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    item: { type: Object, required: true },
});

const form = useForm({
    name: props.item.name,
    description: props.item.description ?? '',
    price_fixed: props.item.price_fixed ?? '',
    price_from: props.item.price_from ?? '',
    price_to: props.item.price_to ?? '',
    duration_minutes: props.item.duration_minutes ?? 60,
});

const submit = () => form.put(`/cabinet/service/items/${props.item.id}`);
const destroy = () => {
    if (confirm('Удалить услугу?')) router.delete(`/cabinet/service/items/${props.item.id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-2xl">
            <div>
                <Link href="/cabinet/service/items" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку услуг
                </Link>
                <h1 class="page-title">Редактирование услуги</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Название услуги" required />
                <div class="grid grid-cols-3 gap-4">
                    <Input v-model="form.price_fixed" label="Фикс. цена, ₽" type="number" />
                    <Input v-model="form.price_from" label="Цена от, ₽" type="number" />
                    <Input v-model="form.price_to" label="Цена до, ₽" type="number" />
                </div>
                <Input v-model="form.duration_minutes" label="Длительность, мин" type="number" />
                <Textarea v-model="form.description" label="Описание" :rows="3" />

                <div class="flex items-center justify-between pt-2 border-t border-outline">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/service/items" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy" class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
