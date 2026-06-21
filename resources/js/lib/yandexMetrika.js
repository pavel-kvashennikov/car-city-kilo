import { router } from '@inertiajs/vue3';

function getCounterId() {
    return document.querySelector('meta[name="yandex-metrika-id"]')?.content ?? null;
}

function hit(url) {
    const id = getCounterId();

    if (!id || typeof window.ym !== 'function') {
        return;
    }

    window.ym(Number(id), 'hit', url, {
        referer: document.referrer,
    });
}

export function setupYandexMetrika() {
    if (!getCounterId()) {
        return;
    }

    router.on('navigate', (event) => {
        hit(event.detail.page.url);
    });
}
