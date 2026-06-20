<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    specialization: '',
});

const submit = () => form.post('/cabinet/service/masters');
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-lg">
            <div>
                <Link href="/cabinet/service/masters" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку мастеров
                </Link>
                <h1 class="page-title">Добавить мастера</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Имя мастера" :error="form.errors.name" required />
                <Input v-model="form.specialization" label="Специализация" :error="form.errors.specialization" placeholder="Слесарь, электрик, маляр..." />

                <div class="flex items-center gap-3 pt-2 border-t border-outline">
                    <Button type="submit" :loading="form.processing">Добавить</Button>
                    <Link href="/cabinet/service/masters" class="btn-secondary !text-sm">Отмена</Link>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
