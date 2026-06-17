import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useFilters(initialFilters = {}, url = '') {
    const filters = reactive({ ...initialFilters })

    const activeCount = computed(() =>
        Object.values(filters).filter(v => v !== '' && v !== null && v !== undefined).length
    )

    const hasActiveFilters = computed(() => activeCount.value > 0)

    const apply = (targetUrl = null) => {
        const cleanFilters = Object.fromEntries(
            Object.entries(filters).filter(([, v]) => v !== '' && v !== null && v !== undefined)
        )
        router.get(targetUrl || url || window.location.pathname, cleanFilters, {
            preserveState: true,
            preserveScroll: true,
        })
    }

    const reset = () => {
        Object.keys(filters).forEach(key => { filters[key] = '' })
        apply()
    }

    const setFilter = (key, value) => {
        filters[key] = value
        apply()
    }

    return {
        filters,
        activeCount,
        hasActiveFilters,
        apply,
        reset,
        setFilter,
    }
}
