import { Head } from '@inertiajs/react';
import {
    create,
    index,
} from '@/actions/App/Http/Controllers/AccountController';
import AccountForm from '@/components/account-form';

export default function CreateAccount({
    accountTypes,
    currencies,
}: {
    accountTypes: string[];
    currencies: string[];
}) {
    return (
        <>
            <Head title="Add account" />
            <div className="max-w-3xl space-y-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Add account</h1>
                    <p className="text-sm text-muted-foreground">
                        Store the details securely for future use.
                    </p>
                </div>
                <AccountForm
                    accountTypes={accountTypes}
                    currencies={currencies}
                />
            </div>
        </>
    );
}

CreateAccount.layout = {
    breadcrumbs: [
        { title: 'Accounts', href: index() },
        { title: 'Add account', href: create() },
    ],
};
