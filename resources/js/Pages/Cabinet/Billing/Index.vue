<script setup>
import CabinetLayout from '@/Components/Shared/CabinetLayout.vue'
import Badge from '@/Components/UI/Badge.vue'

defineProps({
    subscription: { type: Object, default: () => null },
    payments: { type: Array, default: () => [] },
    plans: { type: Array, default: () => [] },
})

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v / 100) + ' ₽'
</script>

<template>
    <CabinetLayout>
        <h1 class="text-2xl font-bold mb-6">Биллинг</h1>

        <div v-if="subscription" class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-semibold mb-2">Текущая подписка</h2>
            <p class="text-lg">{{ subscription.plan?.name }}</p>
            <p class="text-sm text-gray-500">
                Действует до: {{ subscription.ends_at }}
            </p>
            <Badge :variant="subscription.is_active ? 'success' : 'danger'" class="mt-2">
                {{ subscription.is_active ? 'Активна' : 'Истекла' }}
            </Badge>
        </div>

        <div v-if="plans.length" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-lg">{{ plan.name }}</h3>
                <p class="text-2xl font-bold text-blue-600 mt-2">{{ formatPrice(plan.price_monthly) }}/мес</p>
                <ul class="mt-4 space-y-1 text-sm text-gray-600">
                    <li v-for="(feature, i) in (plan.features || [])" :key="i">{{ feature }}</li>
                </ul>
                <button class="w-full mt-4 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-sm">
                    Выбрать
                </button>
            </div>
        </div>

        <div v-if="payments.length">
            <h2 class="font-semibold mb-4">История платежей</h2>
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="payment in payments" :key="payment.id">
                            <td class="px-4 py-3 text-sm">{{ payment.created_at }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ formatPrice(payment.amount) }}</td>
                            <td class="px-4 py-3">
                                <Badge :variant="payment.status === 'paid' ? 'success' : 'warning'">
                                    {{ payment.status }}
                                </Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </CabinetLayout>
</template>
