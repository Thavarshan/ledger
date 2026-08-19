import { Head } from '@inertiajs/react';
import {
    create,
    index,
} from '@/actions/App/Http/Controllers/TransactionController';
import TransactionForm from '@/components/transaction-form';
import { Card, CardContent } from '@/components/ui/card';
import type { AccountOption, Transaction } from '@/types';

export default function CreateTransaction({
    accounts,
    directions,
}: {
    accounts: AccountOption[];
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
                <Card className="max-w-2xl">
                    <CardContent>
                        <TransactionForm
                            accounts={accounts}
                            directions={directions}
                        />
                    </CardContent>
                </Card>
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
