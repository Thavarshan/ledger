import { render, screen } from '@testing-library/react';
import { createElement } from 'react';
import { describe, expect, it, vi } from 'vitest';
import Pagination from './pagination';

vi.mock('@inertiajs/react', () => ({
    Link: ({ children }: { children: React.ReactNode }) =>
        createElement('a', { href: '#' }, children),
}));

describe('Pagination', () => {
    it('renders Laravel paginator links from meta.links', () => {
        render(
            <Pagination
                items={{
                    data: [],
                    links: {
                        first: '/accounts?page=1',
                        last: '/accounts?page=2',
                        prev: null,
                        next: '/accounts?page=2',
                    },
                    meta: {
                        current_page: 1,
                        last_page: 2,
                        per_page: 12,
                        total: 13,
                        links: [
                            {
                                active: false,
                                label: '&laquo; Previous',
                                url: null,
                            },
                            {
                                active: true,
                                label: '1',
                                url: '/accounts?page=1',
                            },
                            {
                                active: false,
                                label: '2',
                                url: '/accounts?page=2',
                            },
                            {
                                active: false,
                                label: 'Next &raquo;',
                                url: '/accounts?page=2',
                            },
                        ],
                    },
                }}
            />,
        );

        expect(
            screen.getByRole('navigation', { name: 'Pagination' }),
        ).toBeInTheDocument();
        expect(screen.getByRole('link', { name: '2' })).toHaveAttribute(
            'href',
            '#',
        );
    });
});
