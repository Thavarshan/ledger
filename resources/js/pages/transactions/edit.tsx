import { Head } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/TransactionController';
import TransactionForm from '@/components/transaction-form';
import type { AccountSummary, ResourceResponse, Transaction } from '@/types';

export default function EditTransaction({
    transaction,
    accounts,
    directions,
}: {
    transaction: ResourceResponse<Transaction>;
    accounts: AccountSummary[];
    directions: Transaction['direction'][];
}) {
    return (
        <>
            <Head title="Edit transaction" />
            <div className="space-y-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Edit transaction</h1>
                    <p className="text-sm text-muted-foreground">
                        Update this ledger entry.
                    </p>
                </div>
                <TransactionForm
                    accounts={accounts}
                    directions={directions}
                    transaction={transaction.data}
                />
            </div>
        </>
    );
}

EditTransaction.layout = {
    breadcrumbs: [
        { title: 'Transactions', href: index() },
        { title: 'Edit transaction', href: index() },
    ],
};
