<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const form = useForm({
    name: '',
    specialization: '',
})

const submit = () => {
    form.post('/cabinet/service/masters')
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Добавить мастера</h1>
        <form class="max-w-md bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Имя *" :error="form.errors.name" required />
            <Input v-model="form.specialization" label="Специализация" :error="form.errors.specialization" />
            <Button type="submit" :loading="form.processing">Добавить</Button>
        </form>
    </CabinetLayout>
</template>
