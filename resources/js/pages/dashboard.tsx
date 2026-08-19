import { Head, Link } from '@inertiajs/react';
import { ArrowLeftRight, Landmark, Plus } from 'lucide-react';
import {
    create as createAccount,
    index as accountsIndex,
} from '@/actions/App/Http/Controllers/AccountController';
import {
    create as createTransaction,
    index as transactionsIndex,
} from '@/actions/App/Http/Controllers/TransactionController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import { dashboard } from '@/routes';

export default function Dashboard() {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-2xl font-semibold">Dashboard</h1>
                    <p className="text-sm text-muted-foreground">
                        A starting point for your ledger — add an account and
                        start recording transactions.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Accounts</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Empty className="border-none p-0">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Landmark />
                                    </EmptyMedia>
                                    <EmptyTitle>
                                        Manage your bank accounts
                                    </EmptyTitle>
                                    <EmptyDescription>
                                        Add the accounts you want to track, then
                                        record activity against them.
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <div className="flex gap-2">
                                        <Button asChild>
                                            <Link href={createAccount()}>
                                                <Plus />
                                                Add account
                                            </Link>
                                        </Button>
                                        <Button variant="outline" asChild>
                                            <Link href={accountsIndex()}>
                                                View accounts
                                            </Link>
                                        </Button>
                                    </div>
                                </EmptyContent>
                            </Empty>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Transactions</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Empty className="border-none p-0">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <ArrowLeftRight />
                                    </EmptyMedia>
                                    <EmptyTitle>
                                        Track credits and debits
                                    </EmptyTitle>
                                    <EmptyDescription>
                                        Record a transaction against an account
                                        to start building your ledger history.
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <div className="flex gap-2">
                                        <Button asChild>
                                            <Link href={createTransaction()}>
                                                <Plus />
                                                Add transaction
                                            </Link>
                                        </Button>
                                        <Button variant="outline" asChild>
                                            <Link href={transactionsIndex()}>
                                                View transactions
                                            </Link>
                                        </Button>
                                    </div>
                                </EmptyContent>
                            </Empty>
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Getting started</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ol className="list-inside list-decimal space-y-1 text-sm text-muted-foreground">
                            <li>Add a bank account with its currency.</li>
                            <li>
                                Record a credit or debit transaction against it.
                            </li>
                            <li>
                                Come back here as your ledger grows to keep an
                                eye on things.
                            </li>
                        </ol>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
