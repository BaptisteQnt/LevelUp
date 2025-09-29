import { router } from '@inertiajs/vue3';

const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

if (token) {
    router.on('before', (event) => {
        event.detail.visit.headers['X-CSRF-TOKEN'] = token.content;
        event.detail.visit.headers['X-Requested-With'] = 'XMLHttpRequest';
    });
} else {
    console.warn('CSRF token not found. Inertia requests may be rejected.');
}
