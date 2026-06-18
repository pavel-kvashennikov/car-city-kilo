<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/Components/UI/Input.vue'
import Button from '@/Components/UI/Button.vue'

const props = defineProps({
    staffMember: { type: Object, required: true },
})

const form = useForm({
    position: props.staffMember.pivot?.position ?? '',
})

const submit = () => {
    form.put(`/cabinet/staff/${props.staffMember.id}`)
}
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Редактирование сотрудника</h1>
        <form class="max-w-md bg-white rounded-xl shadow p-6 space-y-4" @submit.prevent="submit">
            <div>
                <p class="text-sm text-gray-500">Имя</p>
                <p class="font-medium">{{ staffMember.name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ staffMember.email }}</p>
            </div>
            <Input v-model="form.position" label="Должность" :error="form.errors.position" />
            <Button type="submit" :loading="form.processing">Сохранить</Button>
        </form>
    </CabinetLayout>
</template>
