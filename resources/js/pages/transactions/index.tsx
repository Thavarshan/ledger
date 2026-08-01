import { Form, Head, Link } from '@inertiajs/react';
import {
    create,
    edit,
    index,
    show,
} from '@/actions/App/Http/Controllers/TransactionController';
import Pagination from '@/components/pagination';
import { Button } from '@/components/ui/button';
import type { AccountSummary, Paginated, Transaction } from '@/types';

export default function TransactionsIndex({
    transactions,
    accounts,
    directions,
}: {
    transactions: Paginated<Transaction>;
    accounts: AccountSummary[];
    directions: Transaction['direction'][];
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
                        <Link href={create()}>Add transaction</Link>
                    </Button>
                </div>
                <Form
                    action={index()}
                    className="flex flex-wrap gap-3 rounded-xl border p-3"
                >
                    <input
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="search"
                        placeholder="Search transactions"
                    />
                    <select
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="account_id"
                        defaultValue=""
                    >
                        <option value="">All accounts</option>
                        {accounts.map((account) => (
                            <option key={account.id} value={account.id}>
                                {account.name}
                            </option>
                        ))}
                    </select>
                    <select
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="direction"
                        defaultValue=""
                    >
                        <option value="">All directions</option>
                        {directions.map((direction) => (
                            <option key={direction} value={direction}>
                                {direction}
                            </option>
                        ))}
                    </select>
                    <select
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="sort"
                        defaultValue=""
                    >
                        <option value="">Newest</option>
                        <option value="occurred_at:asc">Oldest</option>
                        <option value="amount_minor:desc">Amount</option>
                    </select>
                    <Button type="submit" variant="outline">
                        Apply
                    </Button>
                </Form>
                <div className="overflow-hidden rounded-xl border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3">Description</th>
                                <th className="p-3">Account</th>
                                <th className="p-3">Direction</th>
                                <th className="p-3">Amount</th>
                                <th className="p-3">Occurred</th>
                            </tr>
                        </thead>
                        <tbody>
                            {transactions.data.map((transaction) => (
                                <tr key={transaction.id} className="border-t">
                                    <td className="p-3">
                                        <Link
                                            className="font-medium hover:underline"
                                            href={show(transaction)}
                                        >
                                            {transaction.description}
                                        </Link>
                                    </td>
                                    <td className="p-3">
                                        {transaction.account?.name}
                                    </td>
                                    <td className="p-3 capitalize">
                                        {transaction.direction}
                                    </td>
                                    <td className="p-3">
                                        {transaction.amount_minor}
                                    </td>
                                    <td className="p-3">
                                        {new Date(
                                            transaction.occurred_at,
                                        ).toLocaleString()}{' '}
                                        <Link
                                            className="ml-2 text-primary hover:underline"
                                            href={edit(transaction)}
                                        >
                                            Edit
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    {transactions.data.length === 0 && (
                        <p className="p-6 text-center text-sm text-muted-foreground">
                            No transactions yet.
                        </p>
                    )}
                    <Pagination items={transactions} />
                </div>
            </div>
        </>
    );
}

TransactionsIndex.layout = {
    breadcrumbs: [{ title: 'Transactions', href: index() }],
};
