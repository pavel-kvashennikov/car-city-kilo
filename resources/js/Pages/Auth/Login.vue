<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { Car, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({ email: '', password: '', remember: false });
const showPassword = ref(false);

function submit() {
    form.post('/login');
}
</script>

<template>
    <div class="min-h-screen bg-surface flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shadow-md">
                        <Car class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-xl font-extrabold text-gradient">Город машин</span>
                </Link>
                <h1 class="text-xl font-bold text-on-surface mt-5">Добро пожаловать</h1>
                <p class="text-sm text-on-surface-muted mt-1">Войдите в свой аккаунт</p>
            </div>

            <div class="card p-6 space-y-4">
                <div v-if="Object.keys(form.errors).length" class="p-3 rounded-xl bg-danger-bg text-danger text-sm">
                    {{ form.errors.email || form.errors.password }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="input-field"
                            placeholder="you@example.com"
                            required
                            autocomplete="email"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Пароль</label>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="input-field pr-10"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary"
                            >
                                <component :is="showPassword ? EyeOff : Eye" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.remember" type="checkbox" class="rounded border-outline text-primary focus:ring-primary w-4 h-4" />
                            <span class="text-sm text-on-surface-muted">Запомнить</span>
                        </label>
                        <Link href="/password/reset" class="text-sm text-primary hover:underline font-medium">Забыли пароль?</Link>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full btn-primary !py-2.5 !justify-center !text-sm">
                        {{ form.processing ? 'Вход...' : 'Войти' }}
                    </button>
                </form>

                <p class="text-center text-sm text-on-surface-muted pt-1">
                    Нет аккаунта?
                    <Link href="/register" class="text-primary font-semibold hover:underline">Зарегистрироваться</Link>
                </p>
            </div>
        </div>
    </div>
</template>
