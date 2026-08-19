import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { createElement } from 'react';
import { describe, expect, it, vi } from 'vitest';
import type { TransactionWithAccount } from '@/types';
import { TransactionsTable } from './transactions-table';

vi.mock('@inertiajs/react', () => ({
    Link: ({ children, href }: { children: React.ReactNode; href: string }) =>
        createElement('a', { href }, children),
    router: { delete: vi.fn() },
}));

const transactions: TransactionWithAccount[] = [
    {
        id: 1,
        account_id: 1,
        direction: 'credit',
        amount_minor: '150000',
        description: 'Salary',
        reference: null,
        notes: null,
        occurred_at: '2026-08-01T10:30:00Z',
        account: {
            id: 1,
            name: 'Everyday Savings',
            currency_code: 'LKR',
        },
        created_at: null,
        updated_at: null,
    },
];

describe('TransactionsTable', () => {
    it('shows an empty state when there are no transactions', () => {
        render(<TransactionsTable transactions={[]} />);

        expect(screen.getByText('No transactions yet')).toBeInTheDocument();
    });

    it('renders transaction rows with direction and formatted amount', () => {
        render(<TransactionsTable transactions={transactions} />);

        expect(
            screen.getByRole('link', { name: 'Salary' }),
        ).toBeInTheDocument();
        expect(screen.getByText('Everyday Savings')).toBeInTheDocument();
        expect(screen.getByText('Credit')).toBeInTheDocument();
    });

    it('opens the delete confirmation from the row actions menu', async () => {
        const user = userEvent.setup();
        render(<TransactionsTable transactions={transactions} />);

        await user.click(screen.getByRole('button', { name: 'Open actions' }));
        await user.click(screen.getByRole('menuitem', { name: 'Delete' }));

        expect(
            screen.getByRole('heading', { name: 'Delete transaction?' }),
        ).toBeInTheDocument();
    });
});
