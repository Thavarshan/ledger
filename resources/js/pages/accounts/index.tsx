import { Head, Link } from '@inertiajs/react';
import {
    create,
    edit,
    index,
    show,
} from '@/actions/App/Http/Controllers/AccountController';
import type { Account } from '@/components/account-form';
import { Button } from '@/components/ui/button';

type Props = {
    accounts: { data: Account[] };
    accountTypes: string[];
    currencies: string[];
};

export default function AccountsIndex({ accounts }: Props) {
    return (
        <>
            <Head title="Accounts" />
            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">Accounts</h1>
                        <p className="text-sm text-muted-foreground">
                            Your bank accounts and payment details.
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={create()}>Add account</Link>
                    </Button>
                </div>
                <div className="overflow-hidden rounded-xl border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="p-3">Account</th>
                                <th className="p-3">Bank</th>
                                <th className="p-3">Currency</th>
                                <th className="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {accounts.data.map((account) => (
                                <tr key={account.id} className="border-t">
                                    <td className="p-3">
                                        <Link
                                            className="font-medium hover:underline"
                                            href={show(account)}
                                        >
                                            {account.name}
                                        </Link>
                                        <div className="text-muted-foreground">
                                            •••• {account.account_number_last4}
                                        </div>
                                    </td>
                                    <td className="p-3">{account.bank_name}</td>
                                    <td className="p-3">
                                        {account.currency_code}
                                    </td>
                                    <td className="p-3">
                                        {account.is_primary
                                            ? 'Primary'
                                            : account.is_active
                                              ? 'Active'
                                              : 'Inactive'}{' '}
                                        <Link
                                            className="ml-2 text-primary hover:underline"
                                            href={edit(account)}
                                        >
                                            Edit
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    {accounts.data.length === 0 && (
                        <p className="p-6 text-center text-sm text-muted-foreground">
                            No accounts yet.
                        </p>
                    )}
                </div>
            </div>
        </>
    );
}

AccountsIndex.layout = { breadcrumbs: [{ title: 'Accounts', href: index() }] };
