import { Head } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/account-form';
import type { Account } from '@/components/account-form';

export default function EditAccount({
    account,
    accountTypes,
    currencies,
}: {
    account: Account;
    accountTypes: string[];
    currencies: string[];
}) {
    return (
        <>
            <Head title={`Edit ${account.name}`} />
            <div className="max-w-3xl space-y-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Edit account</h1>
                    <p className="text-sm text-muted-foreground">
                        Update the account details.
                    </p>
                </div>
                <AccountForm
                    account={account}
                    accountTypes={accountTypes}
                    currencies={currencies}
                />
            </div>
        </>
    );
}

EditAccount.layout = {
    breadcrumbs: [
        { title: 'Accounts', href: index() },
        { title: 'Edit account', href: index() },
    ],
};
