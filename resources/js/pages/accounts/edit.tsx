import { Head } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/account-form';
import { Card, CardContent } from '@/components/ui/card';
import type { Account, ResourceResponse } from '@/types';

export default function EditAccount({
    account,
    accountTypes,
    currencies,
}: {
    account: ResourceResponse<Account>;
    accountTypes: string[];
    currencies: string[];
}) {
    const item = account.data;

    return (
        <>
            <Head title={`Edit ${item.name}`} />
            <div className="max-w-3xl space-y-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Edit account</h1>
                    <p className="text-sm text-muted-foreground">
                        Update the account details.
                    </p>
                </div>
                <Card>
                    <CardContent>
                        <AccountForm
                            account={item}
                            accountTypes={accountTypes}
                            currencies={currencies}
                        />
                    </CardContent>
                </Card>
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
