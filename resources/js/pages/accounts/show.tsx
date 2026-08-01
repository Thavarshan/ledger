import { Head, Link } from '@inertiajs/react';
import {
    destroy,
    edit,
    index,
} from '@/actions/App/Http/Controllers/AccountController';
import type { Account } from '@/components/account-form';
import { Button } from '@/components/ui/button';

export default function ShowAccount({ account }: { account: Account }) {
    return (
        <>
            <Head title={account.name} />
            <div className="max-w-3xl space-y-6 p-4">
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {account.name}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            {account.bank_name} · {account.currency_code}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={edit(account)}>Edit</Link>
                        </Button>
                        <Button variant="destructive" asChild>
                            <Link
                                href={destroy(account)}
                                method="delete"
                                as="button"
                            >
                                Delete
                            </Link>
                        </Button>
                    </div>
                </div>
                <dl className="grid gap-4 rounded-xl border p-5 sm:grid-cols-2">
                    <Detail
                        label="Account holder"
                        value={account.account_holder_name}
                    />
                    <Detail
                        label="Account number"
                        value={
                            account.account_number_last4
                                ? `•••• ${account.account_number_last4}`
                                : null
                        }
                    />
                    <Detail label="Country" value={account.country_code} />
                    <Detail label="SWIFT / BIC" value={account.swift_bic} />
                    <Detail
                        label="Primary"
                        value={account.is_primary ? 'Yes' : 'No'}
                    />
                    <Detail
                        label="Status"
                        value={account.is_active ? 'Active' : 'Inactive'}
                    />
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

ShowAccount.layout = {
    breadcrumbs: [
        { title: 'Accounts', href: index() },
        { title: 'Account', href: index() },
    ],
};
