import { Form, Head, Link } from '@inertiajs/react';
import {
    create,
    edit,
    index,
    show,
} from '@/actions/App/Http/Controllers/AccountController';
import Pagination from '@/components/pagination';
import { Button } from '@/components/ui/button';
import type { AccountSummary, Paginated } from '@/types';

type Props = {
    accounts: Paginated<AccountSummary>;
    accountTypes: string[];
    currencies: string[];
};

export default function AccountsIndex({
    accounts,
    accountTypes,
    currencies,
}: Props) {
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
                <Form
                    action={index()}
                    className="flex flex-wrap gap-3 rounded-xl border p-3"
                >
                    <input
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="search"
                        placeholder="Search accounts"
                    />
                    <select
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="account_type"
                        defaultValue=""
                    >
                        <option value="">All types</option>
                        {accountTypes.map((type) => (
                            <option key={type} value={type}>
                                {type.replace('_', ' ')}
                            </option>
                        ))}
                    </select>
                    <select
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="currency_code"
                        defaultValue=""
                    >
                        <option value="">All currencies</option>
                        {currencies.map((currency) => (
                            <option key={currency} value={currency}>
                                {currency}
                            </option>
                        ))}
                    </select>
                    <select
                        className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        name="sort"
                        defaultValue=""
                    >
                        <option value="">Newest</option>
                        <option value="name:asc">Name</option>
                        <option value="bank_name:asc">Bank</option>
                    </select>
                    <Button type="submit" variant="outline">
                        Apply
                    </Button>
                </Form>
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
                    <Pagination items={accounts} />
                </div>
            </div>
        </>
    );
}

AccountsIndex.layout = { breadcrumbs: [{ title: 'Accounts', href: index() }] };
