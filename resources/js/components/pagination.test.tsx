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
        expect(screen.getByText('Showing 1–12 of 13')).toBeInTheDocument();
    });

    it('renders nothing when there are no results', () => {
        const { container } = render(
            <Pagination
                items={{
                    data: [],
                    links: { first: null, last: null, prev: null, next: null },
                    meta: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 12,
                        total: 0,
                        links: [],
                    },
                }}
            />,
        );

        expect(container).toBeEmptyDOMElement();
    });

    it('shows the result range without page links for a single page', () => {
        render(
            <Pagination
                items={{
                    data: [],
                    links: { first: null, last: null, prev: null, next: null },
                    meta: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 12,
                        total: 5,
                        links: [{ active: true, label: '1', url: '/accounts' }],
                    },
                }}
            />,
        );

        expect(screen.getByText('Showing 1–5 of 5')).toBeInTheDocument();
        expect(
            screen.queryByRole('navigation', { name: 'Pagination' }),
        ).not.toBeInTheDocument();
    });
});
