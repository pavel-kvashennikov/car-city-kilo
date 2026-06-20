<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { Link } from '@inertiajs/vue3';
import { Plus, UserCircle, Users } from 'lucide-vue-next';

defineProps({
    masters: { type: Object, default: () => ({ data: [], links: [] }) },
});

const DAYS = ['mon','tue','wed','thu','fri','sat','sun'];
const DAY_LABELS = { mon:'Пн', tue:'Вт', wed:'Ср', thu:'Чт', fri:'Пт', sat:'Сб', sun:'Вс' };

function activeDays(schedule) {
    if (!schedule) return null;
    return DAYS.filter(d => schedule[d]?.active).map(d => DAY_LABELS[d]).join(', ');
}
</script>

<template>
    <CabinetLayout>
        <div class="space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">Мастера</h1>
                    <p class="page-subtitle">Сотрудники сервиса, доступные для записи клиентов</p>
                </div>
                <Link href="/cabinet/service/masters/create" class="btn-primary !text-sm">
                    <Plus class="w-4 h-4" /> Добавить мастера
                </Link>
            </div>

            <!-- Empty state -->
            <div v-if="!masters.data?.length && !masters.length" class="card p-16 text-center">
                <Users class="w-12 h-12 text-outline mx-auto mb-3" />
                <p class="text-on-surface-muted font-medium">Мастера не добавлены</p>
                <p class="text-sm text-on-surface-muted mt-1 mb-4">
                    Добавьте мастеров — клиенты смогут выбирать их при онлайн-записи
                </p>
                <Link href="/cabinet/service/masters/create" class="btn-primary !text-sm inline-flex">
                    <Plus class="w-4 h-4" /> Добавить первого мастера
                </Link>
            </div>

            <!-- List -->
            <div v-else class="card overflow-hidden">
                <div class="divide-y divide-outline">
                    <div v-for="master in (masters.data ?? masters)" :key="master.id"
                        class="flex items-center gap-4 px-5 py-4 hover:bg-surface-muted/40 transition-colors"
                        :class="{ 'opacity-60': !master.is_active }">

                        <!-- Avatar -->
                        <div class="w-11 h-11 rounded-xl overflow-hidden bg-surface-muted shrink-0 flex items-center justify-center">
                            <img v-if="master.photo_path"
                                :src="`/storage/${master.photo_path}`"
                                class="w-full h-full object-cover" />
                            <UserCircle v-else class="w-7 h-7 text-outline" />
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-sm text-on-surface">{{ master.name }}</p>
                                <span v-if="!master.is_active"
                                    class="text-xs px-2 py-0.5 rounded-full badge-neutral">Неактивен</span>
                            </div>
                            <p v-if="master.specialization" class="text-xs text-on-surface-muted mt-0.5">
                                {{ master.specialization }}
                            </p>
                            <p v-if="activeDays(master.schedule)" class="text-xs text-on-surface-muted mt-0.5">
                                Работает: {{ activeDays(master.schedule) }}
                            </p>
                        </div>

                        <!-- Edit -->
                        <Link :href="`/cabinet/service/masters/${master.id}/edit`"
                            class="text-xs font-semibold text-primary hover:underline shrink-0">
                            Изменить
                        </Link>
                    </div>
                </div>
            </div>

            <Pagination v-if="masters.links" :links="masters.links" />
        </div>
    </CabinetLayout>
</template>
