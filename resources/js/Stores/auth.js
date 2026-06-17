import { defineStore } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export const useAuthStore = defineStore('auth', {
    getters: {
        user: () => {
            const page = usePage()
            return computed(() => page.props.auth?.user)
        },

        isAuthenticated: () => {
            const page = usePage()
            return computed(() => !!page.props.auth?.user)
        },

        roles: () => {
            const page = usePage()
            return computed(() => page.props.auth?.user?.roles ?? [])
        },

        hasRole: () => (role) => {
            const page = usePage()
            const roles = page.props.auth?.user?.roles ?? []
            return roles.some(r => r.name === role)
        },

        isAdmin: () => {
            const page = usePage()
            const roles = page.props.auth?.user?.roles ?? []
            return computed(() => roles.some(r => r.name === 'super_admin' || r.name === 'admin'))
        },
    },
})
