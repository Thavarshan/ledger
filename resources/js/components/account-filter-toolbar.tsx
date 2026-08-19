import { Form, Link } from '@inertiajs/react';
import { Search, X } from 'lucide-react';
import { index } from '@/actions/App/Http/Controllers/AccountController';
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
import { useQueryParams } from '@/hooks/use-query-params';
import { humanize } from '@/lib/text';

const SORT_OPTIONS: SortOption[] = [
    { value: 'created_at:desc', label: 'Newest' },
    { value: 'name:asc', label: 'Name (A–Z)' },
    { value: 'name:desc', label: 'Name (Z–A)' },
    { value: 'bank_name:asc', label: 'Bank (A–Z)' },
    { value: 'currency_code:asc', label: 'Currency' },
];

type AccountFilterToolbarProps = {
    accountTypes: string[];
    currencies: string[];
};

export function AccountFilterToolbar({
    accountTypes,
    currencies,
}: AccountFilterToolbarProps) {
    const params = useQueryParams();
    const hasActiveFilters = params.toString() !== '';

    return (
        <Card className="py-4">
            <CardContent>
                <Form
                    action={index()}
                    transform={(data) => {
                        const { account_type, currency_code, ...rest } = data;

                        return {
                            ...rest,
                            ...(account_type !== 'all' && { account_type }),
                            ...(currency_code !== 'all' && { currency_code }),
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
                            placeholder="Search accounts"
                            defaultValue={params.get('search') ?? ''}
                        />
                    </InputGroup>

                    <Select
                        name="account_type"
                        defaultValue={params.get('account_type') ?? 'all'}
                    >
                        <SelectTrigger className="w-full sm:w-40">
                            <SelectValue placeholder="Type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All types</SelectItem>
                            {accountTypes.map((type) => (
                                <SelectItem key={type} value={type}>
                                    {humanize(type)}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select
                        name="currency_code"
                        defaultValue={params.get('currency_code') ?? 'all'}
                    >
                        <SelectTrigger className="w-full sm:w-40">
                            <SelectValue placeholder="Currency" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All currencies</SelectItem>
                            {currencies.map((currency) => (
                                <SelectItem key={currency} value={currency}>
                                    {currency}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <SortSelect
                        defaultValue={params.get('sort') ?? 'created_at:desc'}
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
