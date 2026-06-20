import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Модульный реактивный стейт — обновляется при изменении page props
const vehicleIds = ref(new Set())
const productIds = ref(new Set())
let initialized = false

function getCsrfToken() {
    return decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '')
}

export function useFavorites() {
    const page = usePage()

    if (!initialized) {
        vehicleIds.value = new Set(page.props.auth?.favoritedVehicleIds ?? [])
        productIds.value = new Set(page.props.auth?.favoritedProductIds ?? [])
        initialized = true

        // Синхронизировать при изменении page props (после навигации)
        watch(
            () => page.props.auth?.favoritedVehicleIds,
            (ids) => { vehicleIds.value = new Set(ids ?? []) },
        )
        watch(
            () => page.props.auth?.favoritedProductIds,
            (ids) => { productIds.value = new Set(ids ?? []) },
        )
    }

    const isVehicleFavorited = (id) => vehicleIds.value.has(Number(id))
    const isProductFavorited = (id) => productIds.value.has(Number(id))
    const isLoggedIn = () => !!page.props.auth?.user

    const toggle = async (type, id) => {
        if (!isLoggedIn()) {
            window.location.href = '/login'
            return
        }

        const numId = Number(id)
        const ids = type === 'vehicle' ? vehicleIds : productIds

        // Оптимистичное обновление
        const wasFav = ids.value.has(numId)
        if (wasFav) ids.value.delete(numId)
        else ids.value.add(numId)

        try {
            const res = await fetch('/buyer/favorites/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ type, id: numId }),
            })
            if (!res.ok) throw new Error('Request failed')
        } catch {
            // Откатить при ошибке
            if (wasFav) ids.value.add(numId)
            else ids.value.delete(numId)
        }
    }

    return { isVehicleFavorited, isProductFavorited, isLoggedIn, toggle }
}
