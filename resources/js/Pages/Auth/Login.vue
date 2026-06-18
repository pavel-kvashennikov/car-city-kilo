<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login');
}
</script>

<template>
    <AppLayout>
        <div class="min-h-[65vh] flex items-center justify-center py-16 px-4">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="page-title">Вход в аккаунт</h1>
                    <p class="page-subtitle">Добро пожаловать на AutoMarket</p>
                </div>

                <form class="surface-panel p-8 space-y-5" @submit.prevent="submit">
                    <Input v-model="form.email" label="Email" type="email" :error="form.errors.email" required />
                    <Input v-model="form.password" label="Пароль" type="password" required />

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input v-model="form.remember" type="checkbox" class="rounded border-outline text-primary focus:ring-primary" />
                        <span class="text-sm text-on-surface-muted">Запомнить меня</span>
                    </label>

                    <Button type="submit" :loading="form.processing" class="w-full !py-3">
                        Войти
                    </Button>

                    <p class="text-center text-sm text-on-surface-muted pt-2">
                        Нет аккаунта?
                        <Link href="/register" class="text-primary font-semibold hover:underline">Зарегистрироваться</Link>
                    </p>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
