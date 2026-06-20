<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft, Info } from 'lucide-vue-next';

const form = useForm({
    email: '',
    position: '',
});

const submit = () => form.post('/cabinet/staff');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-lg">
            <div>
                <Link href="/cabinet/staff" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку сотрудников
                </Link>
                <h1 class="page-title">Добавить сотрудника</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.email" label="Email пользователя" type="email" :error="form.errors.email" required />
                <Input v-model="form.position" label="Должность" :error="form.errors.position" placeholder="Менеджер по продажам" />

                <div class="flex items-start gap-2 rounded-xl bg-primary-light/60 p-3 text-xs text-on-surface-muted">
                    <Info class="w-4 h-4 text-primary shrink-0 mt-0.5" />
                    Пользователь должен быть уже зарегистрирован в системе. Он получит доступ к управлению вашей компанией.
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Добавить</Button>
                    <Link href="/cabinet/staff" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
