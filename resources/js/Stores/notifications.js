import { defineStore } from 'pinia'

export const useNotificationsStore = defineStore('notifications', {
    state: () => ({
        items: [],
        unreadCount: 0,
    }),

    getters: {
        hasUnread: (state) => state.unreadCount > 0,
        recent: (state) => state.items.slice(0, 10),
    },

    actions: {
        add(notification) {
            this.items.unshift({
                id: Date.now(),
                ...notification,
                read: false,
                createdAt: new Date().toISOString(),
            })
            this.unreadCount++
        },

        markAsRead(id) {
            const item = this.items.find(n => n.id === id)
            if (item && !item.read) {
                item.read = true
                this.unreadCount = Math.max(0, this.unreadCount - 1)
            }
        },

        markAllAsRead() {
            this.items.forEach(n => { n.read = true })
            this.unreadCount = 0
        },

        remove(id) {
            const item = this.items.find(n => n.id === id)
            if (item && !item.read) {
                this.unreadCount = Math.max(0, this.unreadCount - 1)
            }
            this.items = this.items.filter(n => n.id !== id)
        },

        clear() {
            this.items = []
            this.unreadCount = 0
        },
    },
})
