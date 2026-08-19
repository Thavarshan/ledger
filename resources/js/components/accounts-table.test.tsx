import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { createElement } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { TooltipProvider } from '@/components/ui/tooltip';
import type { AccountListItem } from '@/types';
import { AccountsTable } from './accounts-table';

vi.mock('@inertiajs/react', () => ({
    Link: ({ children, href }: { children: React.ReactNode; href: string }) =>
        createElement('a', { href }, children),
    router: { delete: vi.fn() },
}));

const accounts: AccountListItem[] = [
    {
        id: 1,
        name: 'Everyday Savings',
        bank_name: 'Example Bank',
        currency_code: 'LKR',
        account_number_last4: '4321',
        is_primary: true,
        is_active: true,
        balance_minor: '0',
    },
];

describe('AccountsTable', () => {
    it('shows an empty state when there are no accounts', () => {
        render(
            <TooltipProvider>
                <AccountsTable accounts={[]} />
            </TooltipProvider>,
        );

        expect(screen.getByText('No accounts yet')).toBeInTheDocument();
    });

    it('renders account rows with masked numbers and status badges', () => {
        render(
            <TooltipProvider>
                <AccountsTable accounts={accounts} />
            </TooltipProvider>,
        );

        expect(
            screen.getByRole('link', { name: 'Everyday Savings' }),
        ).toBeInTheDocument();
        expect(screen.getByText('Example Bank')).toBeInTheDocument();
        expect(screen.getByText('LKR')).toBeInTheDocument();
        expect(screen.getByText('Primary')).toBeInTheDocument();
        expect(screen.getByText('Active')).toBeInTheDocument();
    });

    it('opens the delete confirmation from the row actions menu', async () => {
        const user = userEvent.setup();
        render(
            <TooltipProvider>
                <AccountsTable accounts={accounts} />
            </TooltipProvider>,
        );

        await user.click(screen.getByRole('button', { name: 'Open actions' }));
        await user.click(screen.getByRole('menuitem', { name: 'Delete' }));

        expect(
            screen.getByRole('heading', { name: 'Delete account?' }),
        ).toBeInTheDocument();
    });
});
