import { ref } from 'vue'

const notifications = ref([])
let nextId = 0

export function useNotifications() {
    const add = (notification) => {
        const id = ++nextId
        const item = {
            id,
            type: notification.type || 'info',
            title: notification.title || '',
            message: notification.message || '',
            duration: notification.duration || 5000,
        }
        notifications.value.push(item)

        if (item.duration > 0) {
            setTimeout(() => remove(id), item.duration)
        }

        return id
    }

    const remove = (id) => {
        notifications.value = notifications.value.filter(n => n.id !== id)
    }

    const success = (message, title = '') => add({ type: 'success', message, title })
    const error = (message, title = '') => add({ type: 'error', message, title })
    const warning = (message, title = '') => add({ type: 'warning', message, title })
    const info = (message, title = '') => add({ type: 'info', message, title })

    const clear = () => { notifications.value = [] }

    return {
        notifications,
        add,
        remove,
        success,
        error,
        warning,
        info,
        clear,
    }
}
