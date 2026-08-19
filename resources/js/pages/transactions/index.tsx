import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import {
    create,
    index,
} from '@/actions/App/Http/Controllers/TransactionController';
import Pagination from '@/components/pagination';
import { TransactionFilterToolbar } from '@/components/transaction-filter-toolbar';
import { TransactionsTable } from '@/components/transactions-table';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { AccountOption, Paginated, TransactionWithAccount } from '@/types';

export default function TransactionsIndex({
    transactions,
    accounts,
    directions,
}: {
    transactions: Paginated<TransactionWithAccount>;
    accounts: AccountOption[];
    directions: TransactionWithAccount['direction'][];
}) {
    return (
        <>
            <Head title="Transactions" />
            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">Transactions</h1>
                        <p className="text-sm text-muted-foreground">
                            Credits and debits across your accounts.
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={create()}>
                            <Plus />
                            Add transaction
                        </Link>
                    </Button>
                </div>
                <TransactionFilterToolbar
                    accounts={accounts}
                    directions={directions}
                />
                <Card className="py-0">
                    <CardContent className="px-0">
                        <TransactionsTable transactions={transactions.data} />
                        <Pagination items={transactions} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

TransactionsIndex.layout = {
    breadcrumbs: [{ title: 'Transactions', href: index() }],
};
