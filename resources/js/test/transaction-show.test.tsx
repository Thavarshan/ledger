import { render, screen } from '@testing-library/react';
import { createElement } from 'react';
import { describe, expect, it, vi } from 'vitest';
import ShowTransaction from '../pages/transactions/show';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ children }: { children: React.ReactNode }) =>
        createElement('a', { href: '#' }, children),
}));

describe('ShowTransaction', () => {
    it('unwraps the Laravel resource prop before rendering the transaction', () => {
        render(
            <ShowTransaction
                transaction={{
                    data: {
                        id: 1,
                        account_id: 2,
                        direction: 'credit',
                        amount_minor: '2500',
                        description: 'Salary payment',
                        reference: 'PAY-001',
                        notes: null,
                        occurred_at: '2026-08-01T10:15:00.000Z',
                        account: {
                            id: 2,
                            name: 'Main account',
                            currency_code: 'LKR',
                        },
                        created_at: null,
                        updated_at: null,
                    },
                }}
            />,
        );

        expect(
            screen.getByRole('heading', { name: 'Salary payment' }),
        ).toBeInTheDocument();
        expect(screen.getByText('Main account')).toBeInTheDocument();
    });
});
