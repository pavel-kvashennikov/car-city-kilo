<script setup>
defineProps({
    columns: { type: Array, default: () => [] },
    rows: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
})
</script>

<template>
    <div class="surface-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-surface-muted border-b border-outline/30">
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-5 py-3.5 text-left text-xs font-semibold text-on-surface-muted uppercase tracking-wider"
                        >
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline/20">
                    <tr v-if="loading">
                        <td :colspan="columns.length" class="px-5 py-10 text-center text-on-surface-muted">
                            Загрузка...
                        </td>
                    </tr>
                    <tr v-else-if="rows.length === 0">
                        <td :colspan="columns.length" class="px-5 py-10 text-center text-on-surface-muted">
                            Нет данных
                        </td>
                    </tr>
                    <tr
                        v-for="(row, i) in rows"
                        :key="i"
                        class="hover:bg-primary/3 transition-colors"
                        :class="{ 'bg-surface-muted/40': i % 2 === 1 }"
                    >
                        <td v-for="col in columns" :key="col.key" class="px-5 py-3.5 text-sm text-on-surface">
                            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
