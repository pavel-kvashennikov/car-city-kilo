<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    price: { type: Number, required: true },
})

const downPaymentPercent = ref(20)
const termMonths = ref(60)
const rate = ref(12)

const downPayment = computed(() => Math.round(props.price * downPaymentPercent.value / 100))
const loanAmount = computed(() => props.price - downPayment.value)
const monthlyRate = computed(() => rate.value / 100 / 12)

const monthlyPayment = computed(() => {
    if (monthlyRate.value === 0) return Math.round(loanAmount.value / termMonths.value)
    const r = monthlyRate.value
    const n = termMonths.value
    return Math.round(loanAmount.value * (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1))
})

const formatPrice = (v) => new Intl.NumberFormat('ru-RU').format(v) + ' ₽'
</script>

<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">Кредитный калькулятор</h3>
        <div>
            <label class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Первый взнос</span>
                <span class="font-medium">{{ downPaymentPercent }}% ({{ formatPrice(downPayment) }})</span>
            </label>
            <input v-model.number="downPaymentPercent" type="range" min="0" max="90" step="5" class="w-full" />
        </div>
        <div>
            <label class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Срок</span>
                <span class="font-medium">{{ termMonths }} мес.</span>
            </label>
            <input v-model.number="termMonths" type="range" min="12" max="84" step="12" class="w-full" />
        </div>
        <div>
            <label class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Ставка</span>
                <span class="font-medium">{{ rate }}%</span>
            </label>
            <input v-model.number="rate" type="range" min="1" max="30" step="0.5" class="w-full" />
        </div>
        <div class="bg-blue-50 p-4 rounded-lg text-center">
            <p class="text-sm text-gray-600">Ежемесячный платёж</p>
            <p class="text-2xl font-bold text-blue-600">{{ formatPrice(monthlyPayment) }}</p>
        </div>
    </div>
</template>
