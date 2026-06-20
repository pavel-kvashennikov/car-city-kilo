<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Textarea from '@/Components/UI/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    company: { type: Object, required: true },
});

const form = useForm({
    name: props.company.name || '',
    legal_name: props.company.legal_name || '',
    inn: props.company.inn || '',
    description: props.company.description || '',
    phone: props.company.phone || '',
    email: props.company.email || '',
    website: props.company.website || '',
});

const submit = () => form.put('/cabinet/company');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-3xl">
            <div>
                <h1 class="page-title">Профиль компании</h1>
                <p class="page-subtitle">Эти данные видят покупатели в вашей витрине</p>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Название" :error="form.errors.name" required />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.legal_name" label="Юр. название" :error="form.errors.legal_name" />
                    <Input v-model="form.inn" label="ИНН" :error="form.errors.inn" />
                </div>
                <Textarea v-model="form.description" label="Описание" :rows="4" :error="form.errors.description" placeholder="Чем занимается ваша компания, преимущества..." />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <Input v-model="form.phone" label="Телефон" :error="form.errors.phone" />
                    <Input v-model="form.email" label="Email" type="email" :error="form.errors.email" />
                </div>
                <Input v-model="form.website" label="Сайт" :error="form.errors.website" placeholder="https://" />

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Сохранить</Button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
