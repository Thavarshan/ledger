import { Head, Link, router } from '@inertiajs/react';
import { SquarePen, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    destroy,
    edit,
    index,
} from '@/actions/App/Http/Controllers/TransactionController';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { DirectionBadge } from '@/components/direction-badge';
import { TransactionAmount } from '@/components/transaction-amount';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { formatDateTime } from '@/lib/date';
import type { ResourceResponse, TransactionWithAccount } from '@/types';

export default function ShowTransaction({
    transaction,
}: {
    transaction: ResourceResponse<TransactionWithAccount>;
}) {
    const item = transaction.data;
    const [confirmingDelete, setConfirmingDelete] = useState(false);
    const currency = item.account.currency_code;

    return (
        <>
            <Head title={item.description} />
            <div className="max-w-3xl space-y-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {item.description}
                        </h1>
                        <div className="mt-2 flex items-center gap-3">
                            <DirectionBadge direction={item.direction} />
                            <TransactionAmount
                                amountMinor={item.amount_minor}
                                currency={currency}
                                direction={item.direction}
                                className="text-lg"
                            />
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button asChild variant="outline">
                            <Link href={edit(item)}>
                                <SquarePen />
                                Edit
                            </Link>
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={() => setConfirmingDelete(true)}
                        >
                            <Trash2 />
                            Delete
                        </Button>
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <Detail label="Account" value={item.account.name} />
                        <Detail label="Reference" value={item.reference} />
                        <Detail
                            label="Occurred at"
                            value={formatDateTime(item.occurred_at)}
                        />
                    </CardContent>
                </Card>

                {item.notes && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Notes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm whitespace-pre-line">
                                {item.notes}
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>

            <ConfirmDeleteDialog
                open={confirmingDelete}
                onOpenChange={setConfirmingDelete}
                title="Delete transaction?"
                description={`This permanently deletes "${item.description}". This cannot be undone.`}
                onConfirm={() => router.delete(destroy(item).url)}
            />
        </>
    );
}

function Detail({ label, value }: { label: string; value: string | null }) {
    return (
        <div>
            <div className="text-sm text-muted-foreground">{label}</div>
            <div className="font-medium">{value ?? '—'}</div>
        </div>
    );
}

ShowTransaction.layout = {
    breadcrumbs: [
        { title: 'Transactions', href: index() },
        { title: 'Transaction', href: index() },
    ],
};
