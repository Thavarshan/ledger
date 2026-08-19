import { Head } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/TransactionController';
import TransactionForm from '@/components/transaction-form';
import { Card, CardContent } from '@/components/ui/card';
import type {
    AccountOption,
    ResourceResponse,
    Transaction,
    TransactionWithAccount,
} from '@/types';

export default function EditTransaction({
    transaction,
    accounts,
    directions,
}: {
    transaction: ResourceResponse<TransactionWithAccount>;
    accounts: AccountOption[];
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
                <Card className="max-w-2xl">
                    <CardContent>
                        <TransactionForm
                            accounts={accounts}
                            directions={directions}
                            transaction={transaction.data}
                        />
                    </CardContent>
                </Card>
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
