<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const form = useForm({
    email: '',
    position: '',
})

const submit = () => {
    form.post('/cabinet/staff')
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Добавить сотрудника</h1>
        <form class="max-w-md bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.email" label="Email пользователя *" type="email" :error="form.errors.email" required />
            <Input v-model="form.position" label="Должность" :error="form.errors.position" />
            <p class="text-sm text-gray-500">Пользователь должен быть зарегистрирован в системе</p>
            <Button type="submit" :loading="form.processing">Добавить</Button>
        </form>
    </CabinetLayout>
</template>
