import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeftRight, SquarePen, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    destroy,
    edit,
    index,
} from '@/actions/App/Http/Controllers/AccountController';
import { index as transactionsIndex } from '@/actions/App/Http/Controllers/TransactionController';
import { AccountBalance } from '@/components/account-balance';
import { AccountStatusBadges } from '@/components/account-status-badges';
import { AccountTypeBadge } from '@/components/account-type-badge';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Account, ResourceResponse } from '@/types';

export default function ShowAccount({
    account,
}: {
    account: ResourceResponse<Account>;
}) {
    const item = account.data;
    const [confirmingDelete, setConfirmingDelete] = useState(false);

    return (
        <>
            <Head title={item.name} />
            <div className="max-w-3xl space-y-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">{item.name}</h1>
                        {item.balance_minor !== undefined && (
                            <AccountBalance
                                balanceMinor={item.balance_minor}
                                currency={item.currency_code}
                                className="mt-1 block text-3xl font-semibold"
                            />
                        )}
                        <p className="mt-1 text-sm text-muted-foreground">
                            {item.bank_name} · {item.currency_code}
                        </p>
                        <div className="mt-2">
                            <AccountStatusBadges
                                isPrimary={item.is_primary}
                                isActive={item.is_active}
                            />
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
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

                <Button variant="outline" asChild>
                    <Link
                        href={transactionsIndex({
                            query: { account_id: item.id },
                        })}
                    >
                        <ArrowLeftRight />
                        View transactions for this account
                    </Link>
                </Button>

                <Card>
                    <CardHeader>
                        <CardTitle>Identity</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <Detail
                            label="Account holder"
                            value={item.account_holder_name}
                        />
                        <div>
                            <div className="text-sm text-muted-foreground">
                                Account type
                            </div>
                            <AccountTypeBadge type={item.account_type} />
                        </div>
                        <Detail label="Country" value={item.country_code} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Bank details</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <Detail label="Bank" value={item.bank_name} />
                        <Detail label="Bank code" value={item.bank_code} />
                        <Detail label="Branch" value={item.branch_name} />
                        <Detail label="Branch code" value={item.branch_code} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Sensitive identifiers</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <Detail
                            label="Account number"
                            value={
                                item.account_number_last4
                                    ? `•••• ${item.account_number_last4}`
                                    : null
                            }
                        />
                        <Detail label="SWIFT / BIC" value={item.swift_bic} />
                        <Detail
                            label="IBAN"
                            value={item.has_iban ? 'On file' : null}
                        />
                        <Detail
                            label="Routing number"
                            value={item.has_routing_number ? 'On file' : null}
                        />
                        <Detail
                            label="Sort code"
                            value={item.has_sort_code ? 'On file' : null}
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
                title="Delete account?"
                description={`This permanently deletes "${item.name}". This cannot be undone.`}
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

ShowAccount.layout = {
    breadcrumbs: [
        { title: 'Accounts', href: index() },
        { title: 'Account', href: index() },
    ],
};
