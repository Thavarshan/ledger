import { Form, Link } from '@inertiajs/react';
import { Search, X } from 'lucide-react';
import { index } from '@/actions/App/Http/Controllers/TransactionController';
import { SortSelect } from '@/components/sort-select';
import type { SortOption } from '@/components/sort-select';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupInput,
} from '@/components/ui/input-group';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { useQueryParams } from '@/hooks/use-query-params';
import { humanize } from '@/lib/text';
import type { AccountOption, Transaction } from '@/types';

const SORT_OPTIONS: SortOption[] = [
    { value: 'occurred_at:desc', label: 'Newest' },
    { value: 'occurred_at:asc', label: 'Oldest' },
    { value: 'amount_minor:desc', label: 'Amount (high to low)' },
    { value: 'amount_minor:asc', label: 'Amount (low to high)' },
    { value: 'description:asc', label: 'Description (A–Z)' },
];

type TransactionFilterToolbarProps = {
    accounts: AccountOption[];
    directions: Array<Transaction['direction']>;
};

export function TransactionFilterToolbar({
    accounts,
    directions,
}: TransactionFilterToolbarProps) {
    const params = useQueryParams();
    const hasActiveFilters = params.toString() !== '';

    return (
        <Card className="py-4">
            <CardContent>
                <Form
                    action={index()}
                    transform={(data) => {
                        const { account_id, direction, ...rest } = data;

                        return {
                            ...rest,
                            ...(account_id !== 'all' && { account_id }),
                            ...(direction !== 'all' && { direction }),
                        };
                    }}
                    className="flex flex-wrap items-center gap-2"
                >
                    <InputGroup className="w-full sm:w-56">
                        <InputGroupAddon>
                            <Search />
                        </InputGroupAddon>
                        <InputGroupInput
                            name="search"
                            placeholder="Search transactions"
                            defaultValue={params.get('search') ?? ''}
                        />
                    </InputGroup>

                    <Select
                        name="account_id"
                        defaultValue={params.get('account_id') ?? 'all'}
                    >
                        <SelectTrigger className="w-full sm:w-40">
                            <SelectValue placeholder="Account" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All accounts</SelectItem>
                            {accounts.map((account) => (
                                <SelectItem
                                    key={account.id}
                                    value={String(account.id)}
                                >
                                    {account.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select
                        name="direction"
                        defaultValue={params.get('direction') ?? 'all'}
                    >
                        <SelectTrigger className="w-full sm:w-40">
                            <SelectValue placeholder="Direction" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All directions</SelectItem>
                            {directions.map((direction) => (
                                <SelectItem key={direction} value={direction}>
                                    {humanize(direction)}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <DateRangeControl
                        fromName="occurred_from"
                        toName="occurred_to"
                        fromDefault={params.get('occurred_from') ?? ''}
                        toDefault={params.get('occurred_to') ?? ''}
                    />

                    <SortSelect
                        defaultValue={params.get('sort') ?? 'occurred_at:desc'}
                        options={SORT_OPTIONS}
                    />
                    <Button type="submit" className="flex-1 sm:flex-none">
                        Apply
                    </Button>
                    {hasActiveFilters && (
                        <Button variant="ghost" asChild>
                            <Link href={index()}>
                                <X />
                                Reset
                            </Link>
                        </Button>
                    )}
                </Form>
            </CardContent>
        </Card>
    );
}

type DateRangeControlProps = {
    fromName: string;
    toName: string;
    fromDefault: string;
    toDefault: string;
};

function DateRangeControl({
    fromName,
    toName,
    fromDefault,
    toDefault,
}: DateRangeControlProps) {
    const dateInputClassName =
        'h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-sm shadow-none outline-none focus-visible:ring-0';

    return (
        <div className="flex h-9 w-full items-center gap-1.5 rounded-md border border-input bg-transparent px-3 shadow-xs sm:w-auto dark:bg-input/30">
            <span className="text-sm whitespace-nowrap text-muted-foreground">
                From
            </span>
            <input
                type="date"
                name={fromName}
                defaultValue={fromDefault}
                className={dateInputClassName}
            />
            <Separator orientation="vertical" className="h-4" />
            <span className="text-sm whitespace-nowrap text-muted-foreground">
                To
            </span>
            <input
                type="date"
                name={toName}
                defaultValue={toDefault}
                className={dateInputClassName}
            />
        </div>
    );
}
