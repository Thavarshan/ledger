import { Link, router } from '@inertiajs/react';
import { ArrowLeftRight, SquarePen, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    destroy,
    edit,
    show,
} from '@/actions/App/Http/Controllers/TransactionController';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DirectionBadge } from '@/components/direction-badge';
import { RowActionsMenu } from '@/components/row-actions-menu';
import { TransactionAmount } from '@/components/transaction-amount';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDateTime } from '@/lib/date';
import type { TransactionWithAccount } from '@/types';

export function TransactionsTable({
    transactions,
}: {
    transactions: TransactionWithAccount[];
}) {
    const [pendingDeleteId, setPendingDeleteId] = useState<number | null>(null);
    const pendingDeleteTransaction = transactions.find(
        (transaction) => transaction.id === pendingDeleteId,
    );

    if (transactions.length === 0) {
        return (
            <Empty>
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <ArrowLeftRight />
                    </EmptyMedia>
                    <EmptyTitle>No transactions yet</EmptyTitle>
                    <EmptyDescription>
                        Record a credit or debit against one of your accounts.
                    </EmptyDescription>
                </EmptyHeader>
            </Empty>
        );
    }

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Description</TableHead>
                        <TableHead>Account</TableHead>
                        <TableHead>Direction</TableHead>
                        <TableHead className="text-right">Amount</TableHead>
                        <TableHead>Occurred</TableHead>
                        <TableHead className="w-px" />
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {transactions.map((transaction) => (
                        <TableRow key={transaction.id}>
                            <TableCell>
                                <Link
                                    className="font-medium hover:underline"
                                    href={show(transaction)}
                                >
                                    {transaction.description}
                                </Link>
                            </TableCell>
                            <TableCell>{transaction.account.name}</TableCell>
                            <TableCell>
                                <DirectionBadge
                                    direction={transaction.direction}
                                />
                            </TableCell>
                            <TableCell className="text-right">
                                <TransactionAmount
                                    amountMinor={transaction.amount_minor}
                                    currency={transaction.account.currency_code}
                                    direction={transaction.direction}
                                />
                            </TableCell>
                            <TableCell>
                                {formatDateTime(transaction.occurred_at)}
                            </TableCell>
                            <TableCell>
                                <RowActionsMenu
                                    actions={[
                                        {
                                            label: 'Edit',
                                            icon: SquarePen,
                                            href: edit(transaction).url,
                                        },
                                        {
                                            label: 'Delete',
                                            icon: Trash2,
                                            destructive: true,
                                            onSelect: () =>
                                                setPendingDeleteId(
                                                    transaction.id,
                                                ),
                                        },
                                    ]}
                                />
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <ConfirmDeleteDialog
                open={pendingDeleteId !== null}
                onOpenChange={(open) => !open && setPendingDeleteId(null)}
                title="Delete transaction?"
                description={
                    pendingDeleteTransaction
                        ? `This permanently deletes "${pendingDeleteTransaction.description}". This cannot be undone.`
                        : ''
                }
                onConfirm={() => {
                    if (pendingDeleteTransaction) {
                        router.delete(destroy(pendingDeleteTransaction).url);
                    }

                    setPendingDeleteId(null);
                }}
            />
        </>
    );
}
