<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register');
}
</script>

<template>
    <AppLayout>
        <div class="min-h-[65vh] flex items-center justify-center py-16 px-4">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="page-title">Регистрация</h1>
                    <p class="page-subtitle">Создайте аккаунт покупателя</p>
                </div>

                <form class="surface-panel p-8 space-y-4" @submit.prevent="submit">
                    <Input v-model="form.name" label="Имя" :error="form.errors.name" required />
                    <Input v-model="form.email" label="Email" type="email" :error="form.errors.email" required />
                    <Input v-model="form.phone" label="Телефон" type="tel" placeholder="+7 (XXX) XXX-XX-XX" />
                    <Input v-model="form.password" label="Пароль" type="password" :error="form.errors.password" required />
                    <Input v-model="form.password_confirmation" label="Подтвердите пароль" type="password" required />

                    <Button type="submit" :loading="form.processing" class="w-full !py-3 mt-2">
                        Зарегистрироваться
                    </Button>

                    <p class="text-center text-sm text-on-surface-muted pt-2">
                        Уже есть аккаунт?
                        <Link href="/login" class="text-primary font-semibold hover:underline">Войти</Link>
                    </p>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
