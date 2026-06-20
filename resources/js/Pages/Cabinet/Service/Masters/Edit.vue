<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Input from '@/Components/UI/Input.vue';
import Button from '@/Components/UI/Button.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, Trash2, UserCircle } from 'lucide-vue-next';

const props = defineProps({
    master: { type: Object, required: true },
});

const DAYS = [
    { key: 'mon', label: 'Пн' },
    { key: 'tue', label: 'Вт' },
    { key: 'wed', label: 'Ср' },
    { key: 'thu', label: 'Чт' },
    { key: 'fri', label: 'Пт' },
    { key: 'sat', label: 'Сб' },
    { key: 'sun', label: 'Вс' },
];

const defaultDay = (key) => ({
    active: ['mon','tue','wed','thu','fri'].includes(key),
    from: '09:00',
    to: '18:00',
});

const buildSchedule = () => {
    const saved = props.master.schedule ?? {};
    return Object.fromEntries(DAYS.map(d => [d.key, { ...defaultDay(d.key), ...(saved[d.key] ?? {}) }]));
};

const form = useForm({
    _method:        'PUT',
    name:           props.master.name ?? '',
    specialization: props.master.specialization ?? '',
    is_active:      props.master.is_active ?? true,
    schedule:       buildSchedule(),
    photo:          null,
});

const onPhoto = (e) => { form.photo = e.target.files[0] ?? null; };

const submit = () => form.post(`/cabinet/service/masters/${props.master.id}`, {
    forceFormData: true,
});

const destroy = () => {
    if (confirm('Удалить мастера? Это действие необратимо.'))
        router.delete(`/cabinet/service/masters/${props.master.id}`);
};
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6 max-w-2xl">

            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <Link href="/cabinet/service/masters"
                        class="inline-flex items-center gap-1 text-sm text-on-surface-muted hover:text-primary mb-2">
                        <ChevronLeft class="w-3.5 h-3.5" /> К списку мастеров
                    </Link>
                    <h1 class="page-title">{{ master.name }}</h1>
                    <p v-if="master.specialization" class="text-sm text-on-surface-muted mt-0.5">{{ master.specialization }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0"
                    :class="master.is_active ? 'badge-success' : 'badge-neutral'">
                    {{ master.is_active ? 'Активен' : 'Неактивен' }}
                </span>
            </div>

            <form class="space-y-5" @submit.prevent="submit">

                <!-- Основные данные -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">Основные данные</h2>

                    <!-- Photo -->
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-muted shrink-0 flex items-center justify-center">
                            <img v-if="master.photo_path"
                                :src="`/storage/${master.photo_path}`"
                                class="w-full h-full object-cover" />
                            <UserCircle v-else class="w-10 h-10 text-outline" />
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-on-surface-muted mb-1.5 uppercase tracking-wide">
                                Фото мастера
                            </label>
                            <input type="file" accept="image/*" @change="onPhoto"
                                class="block w-full text-sm text-on-surface-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-surface-muted file:text-on-surface hover:file:bg-surface-dim cursor-pointer" />
                            <p v-if="form.errors.photo" class="text-xs text-danger mt-1">{{ form.errors.photo }}</p>
                        </div>
                    </div>

                    <Input v-model="form.name" label="Имя мастера" :error="form.errors.name" required />
                    <Input v-model="form.specialization" label="Специализация"
                        :error="form.errors.specialization"
                        placeholder="Слесарь, электрик, кузовщик..." />

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="w-4 h-4 accent-primary" />
                        <span class="text-sm font-medium text-on-surface">Мастер активен (доступен для записи)</span>
                    </label>
                </div>

                <!-- График работы -->
                <div class="card p-5 space-y-4">
                    <h2 class="font-semibold text-on-surface border-b border-outline pb-3">График работы</h2>

                    <div class="space-y-2">
                        <div v-for="day in DAYS" :key="day.key" class="flex items-center gap-3 py-1.5">
                            <label class="flex items-center gap-2 w-16 cursor-pointer shrink-0">
                                <input type="checkbox" v-model="form.schedule[day.key].active"
                                    class="w-4 h-4 accent-primary" />
                                <span class="text-sm font-medium"
                                    :class="form.schedule[day.key].active ? 'text-on-surface' : 'text-on-surface-muted'">
                                    {{ day.label }}
                                </span>
                            </label>
                            <template v-if="form.schedule[day.key].active">
                                <input type="time" v-model="form.schedule[day.key].from"
                                    class="input-field !py-1.5 !text-sm w-28" />
                                <span class="text-on-surface-muted text-sm">—</span>
                                <input type="time" v-model="form.schedule[day.key].to"
                                    class="input-field !py-1.5 !text-sm w-28" />
                            </template>
                            <span v-else class="text-sm text-on-surface-muted">Выходной</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Button type="submit" :loading="form.processing">Сохранить</Button>
                        <Link href="/cabinet/service/masters" class="btn-secondary !text-sm">Отмена</Link>
                    </div>
                    <button type="button" @click="destroy"
                        class="inline-flex items-center gap-1.5 text-sm text-danger hover:underline">
                        <Trash2 class="w-4 h-4" /> Удалить мастера
                    </button>
                </div>
            </form>
        </div>
    </CabinetLayout>
</template>
