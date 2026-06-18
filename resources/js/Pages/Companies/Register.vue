<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const form = useForm({
    name: '',
    inn: '',
    legal_name: '',
    description: '',
    phone: '',
    email: '',
    profiles: [],
})

const profileOptions = [
    { value: 'dealer', label: 'Автодилер' },
    { value: 'parts', label: 'Запчасти' },
    { value: 'service', label: 'Автосервис' },
]

const toggleProfile = (value) => {
    const idx = form.profiles.indexOf(value)
    if (idx >= 0) {
        form.profiles.splice(idx, 1)
    } else {
        form.profiles.push(value)
    }
}

const submit = () => {
    form.post('/register/company')
}
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-2">Регистрация компании</h1>
            <p class="text-gray-500 mb-6">Заполните анкету для размещения на авторынке</p>

            <form class="bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
                <Input v-model="form.name" label="Название компании *" :error="form.errors.name" required />
                <div class="grid grid-cols-2 gap-4">
                    <Input v-model="form.inn" label="ИНН" :error="form.errors.inn" />
                    <Input v-model="form.legal_name" label="Юридическое название" :error="form.errors.legal_name" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <Input v-model="form.phone" label="Телефон *" :error="form.errors.phone" required />
                    <Input v-model="form.email" label="Email *" type="email" :error="form.errors.email" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Бизнес-профили *</label>
                    <div class="space-y-2">
                        <label
                            v-for="opt in profileOptions"
                            :key="opt.value"
                            class="flex items-center gap-2 cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                :checked="form.profiles.includes(opt.value)"
                                class="rounded border-gray-300"
                                @change="toggleProfile(opt.value)"
                            />
                            <span>{{ opt.label }}</span>
                        </label>
                    </div>
                    <p v-if="form.errors.profiles" class="text-sm text-red-600 mt-1">{{ form.errors.profiles }}</p>
                </div>

                <Button type="submit" :loading="form.processing">Отправить заявку</Button>
            </form>
        </div>
    </AppLayout>
</template>
