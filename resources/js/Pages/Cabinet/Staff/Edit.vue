<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    staffMember: { type: Object, required: true },
});

const form = useForm({
    position: props.staffMember.pivot?.position ?? '',
});

const submit = () => form.put(`/cabinet/staff/${props.staffMember.id}`);
const destroy = () => {
    if (confirm('Убрать сотрудника из компании?')) router.delete(`/cabinet/staff/${props.staffMember.id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5 max-w-lg">
            <div>
                <Link href="/cabinet/staff" class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                    <ChevronLeft class="w-3.5 h-3.5" /> К списку сотрудников
                </Link>
                <h1 class="page-title">Сотрудник</h1>
            </div>

            <form class="card p-6 space-y-5" @submit.prevent="submit">
                <div class="flex items-center gap-3 pb-2">
                    <span class="w-12 h-12 rounded-full bg-primary-light text-primary font-bold flex items-center justify-center shrink-0">
                        {{ staffMember.name?.charAt(0) }}
                    </span>
                    <div>
                        <p class="font-semibold text-on-surface">{{ staffMember.name }}</p>
                        <p class="text-sm text-on-surface-muted">{{ staffMember.email }}</p>
                    </div>
                </div>
                <Input v-model="form.position" label="Должность" :error="form.errors.position" />

                <div class="flex items-center justify-between pt-2 border-t border-outline">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/staff" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy" class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Убрать
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
