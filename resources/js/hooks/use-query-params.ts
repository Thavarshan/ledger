import { usePage } from '@inertiajs/react';

export function useQueryParams(): URLSearchParams {
    const page = usePage();
    const url = new URL(
        page.url,
        typeof window !== 'undefined'
            ? window.location.origin
            : 'http://localhost',
    );

    return url.searchParams;
}
