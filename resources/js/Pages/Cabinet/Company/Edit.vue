<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
    company: { type: Object, required: true },
})

const form = useForm({
    name: props.company.name || '',
    legal_name: props.company.legal_name || '',
    inn: props.company.inn || '',
    description: props.company.description || '',
    phone: props.company.phone || '',
    email: props.company.email || '',
    website: props.company.website || '',
})

const submit = () => {
    form.put(`/cabinet/company/${props.company.id}`)
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Настройки компании</h1>
        <form class="max-w-2xl bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Название" :error="form.errors.name" required />
            <Input v-model="form.legal_name" label="Юр. название" :error="form.errors.legal_name" />
            <Input v-model="form.inn" label="ИНН" :error="form.errors.inn" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-gray-300" />
            </div>
            <Input v-model="form.phone" label="Телефон" :error="form.errors.phone" />
            <Input v-model="form.email" label="Email" type="email" :error="form.errors.email" />
            <Input v-model="form.website" label="Сайт" :error="form.errors.website" />
            <Button type="submit" :loading="form.processing">Сохранить</Button>
        </form>
    </CabinetLayout>
</template>
