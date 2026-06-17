<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';

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
        <div class="min-h-[60vh] flex items-center justify-center py-12 px-4">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-bold text-center mb-8">Вход в аккаунт</h1>

                <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm border p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-blue-600" />
                            <span class="ml-2 text-sm text-gray-600">Запомнить</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors"
                    >
                        Войти
                    </button>

                    <p class="text-center text-sm text-gray-600">
                        Нет аккаунта?
                        <Link href="/register" class="text-blue-600 hover:underline">Зарегистрироваться</Link>
                    </p>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
