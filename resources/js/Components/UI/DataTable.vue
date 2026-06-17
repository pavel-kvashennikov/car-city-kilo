<script setup>
defineProps({
    columns: { type: Array, default: () => [] },
    rows: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
})
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        {{ col.label }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="loading">
                    <td :colspan="columns.length" class="px-4 py-8 text-center text-gray-500">
                        Загрузка...
                    </td>
                </tr>
                <tr v-else-if="rows.length === 0">
                    <td :colspan="columns.length" class="px-4 py-8 text-center text-gray-500">
                        Нет данных
                    </td>
                </tr>
                <tr v-for="(row, i) in rows" :key="i" class="hover:bg-gray-50">
                    <td v-for="col in columns" :key="col.key" class="px-4 py-3 text-sm text-gray-900">
                        <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                            {{ row[col.key] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
