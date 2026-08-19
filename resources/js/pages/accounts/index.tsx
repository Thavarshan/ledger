import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import {
    create,
    index,
} from '@/actions/App/Http/Controllers/AccountController';
import { AccountFilterToolbar } from '@/components/account-filter-toolbar';
import { AccountsTable } from '@/components/accounts-table';
import Pagination from '@/components/pagination';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { AccountListItem, Paginated } from '@/types';

type Props = {
    accounts: Paginated<AccountListItem>;
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
                        <Link href={create()}>
                            <Plus />
                            Add account
                        </Link>
                    </Button>
                </div>
                <AccountFilterToolbar
                    accountTypes={accountTypes}
                    currencies={currencies}
                />
                <Card className="py-0">
                    <CardContent className="px-0">
                        <AccountsTable accounts={accounts.data} />
                        <Pagination items={accounts} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AccountsIndex.layout = { breadcrumbs: [{ title: 'Accounts', href: index() }] };
