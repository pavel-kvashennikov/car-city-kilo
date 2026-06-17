import { ref } from 'vue'
import axios from 'axios'

export function useSearch(endpoint = '/api/search') {
    const query = ref('')
    const results = ref([])
    const loading = ref(false)
    const total = ref(0)

    let debounceTimer = null

    const search = async (q = null) => {
        const searchQuery = q ?? query.value
        if (!searchQuery.trim()) {
            results.value = []
            total.value = 0
            return
        }

        loading.value = true
        try {
            const { data } = await axios.get(endpoint, {
                params: { q: searchQuery },
            })
            results.value = data.hits ?? data.data ?? []
            total.value = data.total ?? results.value.length
        } finally {
            loading.value = false
        }
    }

    const debouncedSearch = (q = null) => {
        clearTimeout(debounceTimer)
        debounceTimer = setTimeout(() => search(q), 300)
    }

    const clear = () => {
        query.value = ''
        results.value = []
        total.value = 0
    }

    return {
        query,
        results,
        loading,
        total,
        search,
        debouncedSearch,
        clear,
    }
}
