<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    master: { type: Object, required: true },
});

const form = useForm({
    name: props.master.name,
    specialization: props.master.specialization ?? '',
    is_active: props.master.is_active ?? true,
});

const submit = () => form.put(`/cabinet/service/masters/${props.master.id}`);
const destroy = () => {
    if (confirm('Удалить мастера?')) router.delete(`/cabinet/service/masters/${props.master.id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-lg">
            <div>
                <Link href="/cabinet/service/masters" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку мастеров
                </Link>
                <h1 class="page-title">Редактирование мастера</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <Input v-model="form.name" label="Имя мастера" required />
                <Input v-model="form.specialization" label="Специализация" />
                <Checkbox v-model="form.is_active" label="Активен (доступен для записи)" />

                <div class="flex items-center justify-between pt-2 border-t border-outline">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/service/masters" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy" class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
