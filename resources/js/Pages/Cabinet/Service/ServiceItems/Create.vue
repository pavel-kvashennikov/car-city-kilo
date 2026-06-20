<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    description: '',
    price_fixed: '',
    price_from: '',
    price_to: '',
    duration_minutes: 60,
});

const submit = () => form.post('/cabinet/service/items');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-2xl">
            <div>
                <Link href="/cabinet/service/items" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку услуг
                </Link>
                <h1 class="page-title">Добавить услугу</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Название услуги" :error="form.errors.name" required />
                <div class="grid grid-cols-3 gap-4">
                    <Input v-model="form.price_fixed" label="Фикс. цена, ₽" type="number" />
                    <Input v-model="form.price_from" label="Цена от, ₽" type="number" />
                    <Input v-model="form.price_to" label="Цена до, ₽" type="number" />
                </div>
                <Input v-model="form.duration_minutes" label="Длительность, мин" type="number" />
                <Textarea v-model="form.description" label="Описание" :rows="3" hint="Укажите либо фиксированную цену, либо диапазон от/до" />

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Создать</Button>
                    <Link href="/cabinet/service/items" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
