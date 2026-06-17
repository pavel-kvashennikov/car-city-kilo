import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        loading: false,
    }),

    getters: {
        itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
        total: (state) => state.items.reduce((sum, item) => sum + item.unit_price * item.quantity, 0),
        isEmpty: (state) => state.items.length === 0,
    },

    actions: {
        async fetchCart() {
            this.loading = true
            try {
                const { data } = await axios.get('/api/cart')
                this.items = data.items ?? []
            } catch {
                //
            } finally {
                this.loading = false
            }
        },

        async addItem(itemableType, itemableId, quantity = 1, unitPrice = 0, snapshot = {}) {
            await axios.post('/api/cart/items', {
                itemable_type: itemableType,
                itemable_id: itemableId,
                quantity,
                unit_price: unitPrice,
                snapshot,
            })
            await this.fetchCart()
        },

        async updateQuantity(itemId, quantity) {
            if (quantity <= 0) {
                return this.removeItem(itemId)
            }
            await axios.patch(`/api/cart/items/${itemId}`, { quantity })
            const item = this.items.find(i => i.id === itemId)
            if (item) item.quantity = quantity
        },

        async removeItem(itemId) {
            await axios.delete(`/api/cart/items/${itemId}`)
            this.items = this.items.filter(i => i.id !== itemId)
        },

        async clearCart() {
            await axios.delete('/api/cart')
            this.items = []
        },
    },
})
