import { ref, computed } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(false)

export function useCart() {
    const itemCount = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))

    const total = computed(() => items.value.reduce((sum, item) => sum + item.unit_price * item.quantity, 0))

    const fetchCart = async () => {
        loading.value = true
        try {
            const { data } = await axios.get('/api/cart')
            items.value = data.items ?? []
        } catch {
            // Cart may not exist yet
        } finally {
            loading.value = false
        }
    }

    const addItem = async (itemableType, itemableId, quantity = 1, unitPrice = 0, snapshot = {}) => {
        const { data } = await axios.post('/api/cart/items', {
            itemable_type: itemableType,
            itemable_id: itemableId,
            quantity,
            unit_price: unitPrice,
            snapshot,
        })
        await fetchCart()
        return data
    }

    const updateQuantity = async (itemId, quantity) => {
        if (quantity <= 0) {
            return removeItem(itemId)
        }
        await axios.patch(`/api/cart/items/${itemId}`, { quantity })
        await fetchCart()
    }

    const removeItem = async (itemId) => {
        await axios.delete(`/api/cart/items/${itemId}`)
        items.value = items.value.filter(i => i.id !== itemId)
    }

    const clearCart = async () => {
        await axios.delete('/api/cart')
        items.value = []
    }

    return {
        items,
        loading,
        itemCount,
        total,
        fetchCart,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
    }
}
