<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
    master: { type: Object, required: true },
})

const form = useForm({
    name: props.master.name,
    specialization: props.master.specialization ?? '',
    is_active: props.master.is_active ?? true,
})

const submit = () => {
    form.put(`/cabinet/service/masters/${props.master.id}`)
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Редактирование мастера</h1>
        <form class="max-w-md bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" label="Имя *" required />
            <Input v-model="form.specialization" label="Специализация" />
            <label class="flex items-center gap-2">
                <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300" />
                <span class="text-sm">Активен</span>
            </label>
            <Button type="submit" :loading="form.processing">Сохранить</Button>
        </form>
    </CabinetLayout>
</template>
