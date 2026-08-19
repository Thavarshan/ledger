import { render, screen } from '@testing-library/react';
import { createElement } from 'react';
import { describe, expect, it, vi } from 'vitest';
import Welcome from '../pages/welcome';

const pageProps = {
    auth: { user: null },
    name: 'Ledger',
};

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({
        children,
        href,
    }: {
        children: React.ReactNode;
        href: string | { url: string };
    }) =>
        createElement(
            'a',
            { href: typeof href === 'string' ? href : href.url },
            children,
        ),
    usePage: () => ({ props: pageProps }),
}));

describe('Welcome', () => {
    it('presents the marketing message and sign-in calls to action', () => {
        render(<Welcome />);

        expect(
            screen.getByRole('heading', {
                name: 'Make every rupee feel accounted for.',
            }),
        ).toBeInTheDocument();
        expect(
            screen.getAllByRole('link', { name: 'Start your ledger' }),
        ).not.toHaveLength(0);
        expect(screen.getByRole('link', { name: 'Log in' })).toHaveAttribute(
            'href',
            '/login',
        );
        expect(
            screen.getByText('All your accounts, one view'),
        ).toBeInTheDocument();
    });
});
