import { Head } from '@inertiajs/react';
import {
    create,
    index,
} from '@/actions/App/Http/Controllers/TransactionController';
import TransactionForm from '@/components/transaction-form';
import type { AccountSummary, Transaction } from '@/types';

export default function CreateTransaction({
    accounts,
    directions,
}: {
    accounts: AccountSummary[];
    directions: Transaction['direction'][];
}) {
    return (
        <>
            <Head title="Add transaction" />
            <div className="space-y-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Add transaction</h1>
                    <p className="text-sm text-muted-foreground">
                        Record a credit or debit against an account.
                    </p>
                </div>
                <TransactionForm accounts={accounts} directions={directions} />
            </div>
        </>
    );
}

CreateTransaction.layout = {
    breadcrumbs: [
        { title: 'Transactions', href: index() },
        { title: 'Add transaction', href: create() },
    ],
};
