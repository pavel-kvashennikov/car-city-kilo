<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue';
import Badge from '@/Components/UI/Badge.vue';
import { router } from '@inertiajs/vue3';
import { Check, CreditCard } from 'lucide-vue-next';

const props = defineProps({
    subscription: { type: Object, default: () => null },
    payments: { type: Array, default: () => [] },
    plans: { type: Array, default: () => [] },
});

const fmt = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽';
const isCurrent = (plan) => props.subscription?.plan?.id === plan.id;
const subscribe = (plan) => router.post('/cabinet/billing/subscribe', { plan_id: plan.id });
</script>

<template>
    <CabinetLayout>
        <div class="space-y-6">
            <div>
                <h1 class="page-title">Биллинг</h1>
                <p class="page-subtitle">Подписка и история платежей</p>
            </div>

            <div v-if="subscription" class="card p-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-bright to-primary flex items-center justify-center">
                        <CreditCard class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-muted uppercase tracking-wide">Текущий тариф</p>
                        <p class="font-bold text-on-surface text-lg">{{ subscription.plan?.name }}</p>
                        <p class="text-xs text-on-surface-muted">Действует до {{ subscription.ends_at }}</p>
                    </div>
                </div>
                <Badge :variant="subscription.is_active ? 'success' : 'danger'">
                    {{ subscription.is_active ? 'Активна' : 'Истекла' }}
                </Badge>
            </div>

            <div v-if="plans.length">
                <h2 class="font-bold text-sm text-on-surface mb-3">Тарифные планы</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="card p-6 flex flex-col"
                        :class="isCurrent(plan) ? 'ring-2 ring-primary-bright' : ''"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-bold text-on-surface">{{ plan.name }}</h3>
                            <Badge v-if="isCurrent(plan)" variant="info">Текущий</Badge>
                        </div>
                        <p class="text-2xl font-extrabold text-gradient mt-2">{{ fmt(plan.price_monthly) }}<span class="text-sm font-medium text-on-surface-muted">/мес</span></p>
                        <ul class="mt-4 space-y-2 text-sm text-on-surface-muted flex-1">
                            <li v-for="(feature, i) in (plan.features || [])" :key="i" class="flex items-start gap-2">
                                <Check class="w-4 h-4 text-success shrink-0 mt-0.5" /> {{ feature }}
                            </li>
                        </ul>
                        <button
                            :disabled="isCurrent(plan)"
                            class="w-full mt-5 btn-primary !justify-center !text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            @click="subscribe(plan)"
                        >
                            {{ isCurrent(plan) ? 'Подключён' : 'Выбрать' }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="payments.length">
                <h2 class="font-bold text-sm text-on-surface mb-3">История платежей</h2>
                <div class="card overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-surface-muted border-b border-outline/30">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Дата</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Сумма</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Статус</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline/20">
                            <tr v-for="payment in payments" :key="payment.id" class="hover:bg-primary/3 transition-colors">
                                <td class="px-5 py-3.5 text-sm">{{ payment.created_at }}</td>
                                <td class="px-5 py-3.5 text-sm font-semibold">{{ fmt(payment.amount) }}</td>
                                <td class="px-5 py-3.5">
                                    <Badge :variant="payment.status === 'paid' ? 'success' : 'warning'">{{ payment.status }}</Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </CabinetLayout>
</template>
