import { Head, Link } from '@inertiajs/react';
import {
    destroy,
    edit,
    index,
} from '@/actions/App/Http/Controllers/TransactionController';
import { Button } from '@/components/ui/button';
import type { ResourceResponse, Transaction } from '@/types';

export default function ShowTransaction({
    transaction,
}: {
    transaction: ResourceResponse<Transaction>;
}) {
    const item = transaction.data;

    return (
        <>
            <Head title={item.description} />
            <div className="max-w-3xl space-y-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {item.description}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            {item.direction} · {item.amount_minor} minor units
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button asChild variant="outline">
                            <Link href={edit(item)}>Edit</Link>
                        </Button>
                        <Button asChild variant="destructive">
                            <Link
                                as="button"
                                href={destroy(item)}
                                method="delete"
                            >
                                Delete
                            </Link>
                        </Button>
                    </div>
                </div>
                <dl className="grid gap-4 rounded-xl border p-5 sm:grid-cols-2">
                    <Detail
                        label="Account"
                        value={item.account?.name ?? null}
                    />
                    <Detail label="Reference" value={item.reference} />
                    <Detail
                        label="Occurred at"
                        value={new Date(item.occurred_at).toLocaleString()}
                    />
                    <Detail label="Notes" value={item.notes} />
                </dl>
            </div>
        </>
    );
}

function Detail({ label, value }: { label: string; value: string | null }) {
    return (
        <div>
            <dt className="text-sm text-muted-foreground">{label}</dt>
            <dd className="font-medium">{value ?? '—'}</dd>
        </div>
    );
}

ShowTransaction.layout = {
    breadcrumbs: [
        { title: 'Transactions', href: index() },
        { title: 'Transaction', href: index() },
    ],
};
