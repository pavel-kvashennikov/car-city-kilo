<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { Car } from 'lucide-vue-next';

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
    <div class="min-h-screen bg-surface flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center shadow-md">
                        <Car class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-xl font-extrabold text-gradient">AutoMarket</span>
                </Link>
                <h1 class="text-xl font-bold text-on-surface mt-5">Создать аккаунт</h1>
                <p class="text-sm text-on-surface-muted mt-1">Регистрация покупателя — это бесплатно</p>
            </div>

            <div class="card p-6 space-y-4">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Имя *</label>
                        <input v-model="form.name" type="text" class="input-field" placeholder="Иван Иванов" required />
                        <p v-if="form.errors.name" class="text-xs text-danger mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Email *</label>
                        <input v-model="form.email" type="email" class="input-field" placeholder="you@example.com" required autocomplete="email" />
                        <p v-if="form.errors.email" class="text-xs text-danger mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Телефон</label>
                        <input v-model="form.phone" type="tel" class="input-field" placeholder="+7 (999) 123-45-67" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Пароль *</label>
                        <input v-model="form.password" type="password" class="input-field" placeholder="Минимум 8 символов" required autocomplete="new-password" />
                        <p v-if="form.errors.password" class="text-xs text-danger mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">Подтвердите пароль *</label>
                        <input v-model="form.password_confirmation" type="password" class="input-field" placeholder="Повторите пароль" required autocomplete="new-password" />
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full btn-primary !py-2.5 !justify-center !text-sm">
                        {{ form.processing ? 'Регистрация...' : 'Зарегистрироваться' }}
                    </button>

                    <p class="text-xs text-center text-on-surface-muted">
                        Нажимая, вы принимаете
                        <a href="#" class="text-primary hover:underline">условия использования</a>
                    </p>
                </form>

                <p class="text-center text-sm text-on-surface-muted pt-1 border-t border-outline">
                    Уже есть аккаунт?
                    <Link href="/login" class="text-primary font-semibold hover:underline">Войти</Link>
                </p>
            </div>

            <!-- Company registration -->
            <div class="mt-5 card p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-on-surface">Регистрация бизнеса</p>
                    <p class="text-xs text-on-surface-muted mt-0.5">Разместите свои товары и услуги</p>
                </div>
                <Link href="/register/company" class="btn-secondary !text-xs !py-1.5 !px-3 shrink-0">
                    Для бизнеса
                </Link>
            </div>
        </div>
    </div>
</template>
