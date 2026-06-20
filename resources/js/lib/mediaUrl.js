export function mediaUrl(path) {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path;
    if (path.startsWith('images/')) return `/${path}`;
    return `/storage/${path}`;
}
