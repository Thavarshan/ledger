import { Head, Link } from '@inertiajs/react';
import {
    ArrowDownRight,
    ArrowUpRight,
    Landmark,
    Plus,
    Wallet,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import {
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Line,
    LineChart,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

import {
    create as createAccount,
    index as accountsIndex,
} from '@/actions/App/Http/Controllers/AccountController';
import { create as createTransaction } from '@/actions/App/Http/Controllers/TransactionController';
import { AccountBalance } from '@/components/account-balance';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { ChartContainer, ChartTooltipContent } from '@/components/ui/chart';
import type { ChartConfig } from '@/components/ui/chart';
import { formatMoney } from '@/lib/money';
import { dashboard } from '@/routes';

type DashboardCurrencyTotals = {
    credits_minor: string;
    debits_minor: string;
    net_minor: string;
};

type DashboardAnalytics = {
    summary: {
        accounts_count: number;
        active_accounts_count: number;
        transactions_count: number;
    };
    currencies: string[];
    accounts: {
        id: number;
        name: string;
        currency_code: string;
        balance_minor: string;
        is_active: boolean;
    }[];
    cash_flow: {
        month: string;
        label: string;
        currencies: Record<string, DashboardCurrencyTotals>;
    }[];
};

type DashboardProps = {
    analytics: DashboardAnalytics;
};

const chartColors = {
    credits: 'var(--color-chart-2)',
    debits: 'var(--color-chart-1)',
    net: 'var(--color-chart-3)',
};

const flowChartConfig: ChartConfig = {
    credits: { label: 'Credits', color: chartColors.credits },
    debits: { label: 'Debits', color: chartColors.debits },
    net: { label: 'Net flow', color: chartColors.net },
};

const accountChartConfig: ChartConfig = {
    balance: { label: 'Balance', color: chartColors.net },
};

export default function Dashboard({ analytics }: DashboardProps) {
    const [selectedCurrency, setSelectedCurrency] = useState(
        analytics.currencies[0] ?? '',
    );

    const selectedTotals = useMemo(() => {
        return analytics.cash_flow.reduce(
            (totals, month) => {
                const current = month.currencies[selectedCurrency];

                return {
                    credits:
                        totals.credits + Number(current?.credits_minor ?? 0),
                    debits: totals.debits + Number(current?.debits_minor ?? 0),
                    net: totals.net + Number(current?.net_minor ?? 0),
                };
            },
            { credits: 0, debits: 0, net: 0 },
        );
    }, [analytics.cash_flow, selectedCurrency]);

    const flowData = analytics.cash_flow.map((month) => {
        const totals = month.currencies[selectedCurrency];

        return {
            month: month.label,
            credits: Number(totals?.credits_minor ?? 0),
            debits: -Number(totals?.debits_minor ?? 0),
            net: Number(totals?.net_minor ?? 0),
        };
    });

    const accountData = analytics.accounts.map((account) => ({
        ...account,
        balance: Number(account.balance_minor),
    }));

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-sm font-medium text-muted-foreground">
                            Financial overview
                        </p>
                        <h1 className="text-3xl font-semibold tracking-tight">
                            Your ledger at a glance
                        </h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Follow your balances and money movement across the
                            last six months.
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button asChild>
                            <Link href={createAccount()}>
                                <Plus />
                                Add account
                            </Link>
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={createTransaction()}>
                                <Plus />
                                Add transaction
                            </Link>
                        </Button>
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-3">
                    <MetricCard
                        label="Accounts"
                        value={String(analytics.summary.accounts_count)}
                        detail={`${analytics.summary.active_accounts_count} active`}
                        icon={<Landmark />}
                    />
                    <MetricCard
                        label="Transactions"
                        value={String(analytics.summary.transactions_count)}
                        detail="Recorded in the last six months"
                        icon={<ArrowUpRight />}
                    />
                    <MetricCard
                        label="Currencies"
                        value={String(analytics.currencies.length)}
                        detail="No exchange rates assumed"
                        icon={<Wallet />}
                    />
                </div>

                {analytics.accounts.length === 0 ? (
                    <EmptyDashboard />
                ) : (
                    <>
                        <div className="grid gap-4 lg:grid-cols-[1.5fr_1fr]">
                            <Card>
                                <CardHeader className="flex-row items-start justify-between space-y-0">
                                    <div>
                                        <CardTitle>Cash flow</CardTitle>
                                        <CardDescription>
                                            Credits, debits, and net movement by
                                            month.
                                        </CardDescription>
                                    </div>
                                    <select
                                        aria-label="Cash flow currency"
                                        className="h-9 rounded-md border bg-background px-3 text-sm"
                                        value={selectedCurrency}
                                        onChange={(event) =>
                                            setSelectedCurrency(
                                                event.target.value,
                                            )
                                        }
                                    >
                                        {analytics.currencies.map(
                                            (currency) => (
                                                <option
                                                    key={currency}
                                                    value={currency}
                                                >
                                                    {currency}
                                                </option>
                                            ),
                                        )}
                                    </select>
                                </CardHeader>
                                <CardContent>
                                    <ChartContainer
                                        config={flowChartConfig}
                                        className="h-[280px] w-full"
                                    >
                                        <LineChart
                                            data={flowData}
                                            margin={{ left: 8, right: 8 }}
                                        >
                                            <CartesianGrid vertical={false} />
                                            <XAxis
                                                dataKey="month"
                                                tickLine={false}
                                                axisLine={false}
                                            />
                                            <YAxis hide />
                                            <Tooltip
                                                content={
                                                    <ChartTooltipContent
                                                        formatter={(value) =>
                                                            formatMoney(
                                                                String(
                                                                    Math.round(
                                                                        value,
                                                                    ),
                                                                ),
                                                                selectedCurrency,
                                                            )
                                                        }
                                                    />
                                                }
                                            />
                                            <Line
                                                dataKey="credits"
                                                type="monotone"
                                                stroke="var(--color-credits)"
                                                strokeWidth={2}
                                                dot={false}
                                            />
                                            <Line
                                                dataKey="debits"
                                                type="monotone"
                                                stroke="var(--color-debits)"
                                                strokeWidth={2}
                                                dot={false}
                                            />
                                            <Line
                                                dataKey="net"
                                                type="monotone"
                                                stroke="var(--color-net)"
                                                strokeWidth={2}
                                                dot={false}
                                            />
                                        </LineChart>
                                    </ChartContainer>
                                    <div className="mt-4 grid grid-cols-3 gap-3 border-t pt-4">
                                        <FlowMetric
                                            label="Credits"
                                            value={selectedTotals.credits}
                                            currency={selectedCurrency}
                                            positive
                                        />
                                        <FlowMetric
                                            label="Debits"
                                            value={selectedTotals.debits}
                                            currency={selectedCurrency}
                                        />
                                        <FlowMetric
                                            label="Net flow"
                                            value={selectedTotals.net}
                                            currency={selectedCurrency}
                                            positive={selectedTotals.net >= 0}
                                        />
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Balance by account</CardTitle>
                                    <CardDescription>
                                        Current balances without currency
                                        conversion.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ChartContainer
                                        config={accountChartConfig}
                                        className="h-[340px] w-full"
                                    >
                                        <BarChart
                                            data={accountData}
                                            layout="vertical"
                                            margin={{ left: 8, right: 8 }}
                                        >
                                            <CartesianGrid horizontal={false} />
                                            <XAxis type="number" hide />
                                            <YAxis
                                                dataKey="name"
                                                type="category"
                                                width={88}
                                                tickLine={false}
                                                axisLine={false}
                                                tick={{ fontSize: 11 }}
                                            />
                                            <Tooltip
                                                content={
                                                    <ChartTooltipContent
                                                        formatter={(
                                                            value,
                                                            _name,
                                                            item,
                                                        ) => {
                                                            const currency =
                                                                item.payload
                                                                    ?.currency_code;

                                                            return formatMoney(
                                                                String(
                                                                    Math.round(
                                                                        value,
                                                                    ),
                                                                ),
                                                                typeof currency ===
                                                                    'string'
                                                                    ? currency
                                                                    : selectedCurrency,
                                                            );
                                                        }}
                                                    />
                                                }
                                            />
                                            <Bar dataKey="balance" radius={4}>
                                                {accountData.map((account) => (
                                                    <Cell
                                                        key={account.id}
                                                        fill={
                                                            account.is_active
                                                                ? chartColors.net
                                                                : 'var(--muted)'
                                                        }
                                                    />
                                                ))}
                                            </Bar>
                                        </BarChart>
                                    </ChartContainer>
                                </CardContent>
                            </Card>
                        </div>

                        <Card>
                            <CardHeader>
                                <CardTitle>Accounts</CardTitle>
                                <CardDescription>
                                    Keep an eye on each balance and its
                                    currency.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                {analytics.accounts.map((account) => (
                                    <div
                                        key={account.id}
                                        className="rounded-lg border p-4"
                                    >
                                        <div className="flex items-center justify-between gap-3">
                                            <p className="truncate text-sm font-medium">
                                                {account.name}
                                            </p>
                                            {!account.is_active && (
                                                <span className="text-xs text-muted-foreground">
                                                    Inactive
                                                </span>
                                            )}
                                        </div>
                                        <AccountBalance
                                            balanceMinor={account.balance_minor}
                                            currency={account.currency_code}
                                            className="mt-2 text-lg"
                                        />
                                        <p className="mt-1 text-xs text-muted-foreground">
                                            {account.currency_code}
                                        </p>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    </>
                )}
            </div>
        </>
    );
}

function MetricCard({
    label,
    value,
    detail,
    icon,
}: {
    label: string;
    value: string;
    detail: string;
    icon: React.ReactNode;
}) {
    return (
        <Card>
            <CardContent className="flex items-center justify-between gap-4 pt-6">
                <div>
                    <p className="text-sm text-muted-foreground">{label}</p>
                    <p className="mt-1 text-2xl font-semibold">{value}</p>
                    <p className="mt-1 text-xs text-muted-foreground">
                        {detail}
                    </p>
                </div>
                <div className="rounded-lg bg-muted p-2 text-muted-foreground">
                    {icon}
                </div>
            </CardContent>
        </Card>
    );
}

function FlowMetric({
    label,
    value,
    currency,
    positive = false,
}: {
    label: string;
    value: number;
    currency: string;
    positive?: boolean;
}) {
    return (
        <div>
            <p className="text-xs text-muted-foreground">{label}</p>
            <p
                className={
                    positive
                        ? 'mt-1 text-sm font-medium text-emerald-600'
                        : 'mt-1 text-sm font-medium text-destructive'
                }
            >
                {formatMoney(String(Math.round(value)), currency)}
            </p>
        </div>
    );
}

function EmptyDashboard() {
    return (
        <Card>
            <CardContent className="flex flex-col items-center justify-center gap-4 py-16 text-center">
                <div className="rounded-full bg-muted p-3 text-muted-foreground">
                    <ArrowDownRight />
                </div>
                <div>
                    <h2 className="text-lg font-semibold">
                        Start building your picture
                    </h2>
                    <p className="mt-1 max-w-md text-sm text-muted-foreground">
                        Add an account and record your first transaction to
                        unlock balance and cash-flow insights.
                    </p>
                </div>
                <div className="flex gap-2">
                    <Button asChild>
                        <Link href={createAccount()}>
                            <Plus />
                            Add account
                        </Link>
                    </Button>
                    <Button variant="outline" asChild>
                        <Link href={accountsIndex()}>View accounts</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
