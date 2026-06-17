import { onMounted, onUnmounted, ref } from 'vue'

export function useEcho(channelName, eventName, callback) {
    const connected = ref(false)
    let channel = null

    onMounted(() => {
        if (typeof window.Echo === 'undefined') {
            return
        }

        channel = window.Echo.private(channelName)
        channel.listen(eventName, (data) => {
            callback(data)
        })
        connected.value = true
    })

    onUnmounted(() => {
        if (channel) {
            channel.stopListening(eventName)
            window.Echo?.leave(channelName)
        }
        connected.value = false
    })

    return { connected }
}
