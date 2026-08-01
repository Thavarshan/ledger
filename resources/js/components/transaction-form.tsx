import { useForm } from '@inertiajs/react';
import {
    store,
    update,
} from '@/actions/App/Http/Controllers/TransactionController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AccountSummary, Transaction } from '@/types';

type Props = {
    accounts: AccountSummary[];
    directions: Array<Transaction['direction']>;
    transaction?: Transaction;
};

export default function TransactionForm({
    accounts,
    directions,
    transaction,
}: Props) {
    const form = useForm({
        account_id: String(transaction?.account_id ?? accounts[0]?.id ?? ''),
        direction: transaction?.direction ?? directions[0] ?? 'debit',
        amount_minor: transaction?.amount_minor ?? '',
        description: transaction?.description ?? '',
        reference: transaction?.reference ?? '',
        notes: transaction?.notes ?? '',
        occurred_at: transaction
            ? toLocalDateTime(transaction.occurred_at)
            : toLocalDateTime(new Date().toISOString()),
    });

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();

        form.transform((data) => ({
            ...data,
            occurred_at: new Date(data.occurred_at).toISOString(),
        }));
        form.submit(transaction ? update(transaction) : store());
    }

    return (
        <form className="grid max-w-2xl gap-6" onSubmit={submit}>
            <Field label="Account" error={form.errors.account_id}>
                <Select
                    value={form.data.account_id}
                    onValueChange={(value) => form.setData('account_id', value)}
                >
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {accounts.map((account) => (
                            <SelectItem
                                key={account.id}
                                value={String(account.id)}
                            >
                                {account.name} ({account.currency_code})
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </Field>
            <Field label="Direction" error={form.errors.direction}>
                <Select
                    value={form.data.direction}
                    onValueChange={(value) =>
                        form.setData(
                            'direction',
                            value as Transaction['direction'],
                        )
                    }
                >
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {directions.map((direction) => (
                            <SelectItem key={direction} value={direction}>
                                {direction}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </Field>
            <Field
                label="Amount (minor units)"
                error={form.errors.amount_minor}
            >
                <Input
                    inputMode="numeric"
                    min="1"
                    type="number"
                    value={form.data.amount_minor}
                    onChange={(event) =>
                        form.setData('amount_minor', event.target.value)
                    }
                    required
                />
            </Field>
            <Field label="Description" error={form.errors.description}>
                <Input
                    value={form.data.description}
                    onChange={(event) =>
                        form.setData('description', event.target.value)
                    }
                    required
                />
            </Field>
            <Field label="Reference" error={form.errors.reference}>
                <Input
                    value={form.data.reference}
                    onChange={(event) =>
                        form.setData('reference', event.target.value)
                    }
                />
            </Field>
            <Field label="Occurred at" error={form.errors.occurred_at}>
                <Input
                    type="datetime-local"
                    value={form.data.occurred_at}
                    onChange={(event) =>
                        form.setData('occurred_at', event.target.value)
                    }
                    required
                />
            </Field>
            <Field label="Notes" error={form.errors.notes}>
                <textarea
                    className="min-h-24 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    value={form.data.notes}
                    onChange={(event) =>
                        form.setData('notes', event.target.value)
                    }
                />
            </Field>
            <Button className="w-fit" disabled={form.processing}>
                {transaction ? 'Save changes' : 'Create transaction'}
            </Button>
        </form>
    );
}

function Field({
    label,
    error,
    children,
}: {
    label: string;
    error?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="grid gap-2">
            <Label>{label}</Label>
            {children}
            <InputError message={error} />
        </div>
    );
}

export function toLocalDateTime(value: string): string {
    const date = new Date(value);
    const offset = date.getTimezoneOffset() * 60_000;

    return new Date(date.getTime() - offset).toISOString().slice(0, 16);
}
