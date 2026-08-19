import { useForm } from '@inertiajs/react';
import {
    store,
    update,
} from '@/actions/App/Http/Controllers/TransactionController';
import { DirectionToggle } from '@/components/direction-toggle';
import { FormField } from '@/components/form-field';
import { TransactionAccountField } from '@/components/transaction-account-field';
import { TransactionAmountField } from '@/components/transaction-amount-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { toDateTimeLocalValue } from '@/lib/date';
import { decimalStringToMinor, minorToDecimalString } from '@/lib/money';
import { humanize } from '@/lib/text';
import type {
    AccountOption,
    Transaction,
    TransactionWithAccount,
} from '@/types';

type Props = {
    accounts: AccountOption[];
    directions: Array<Transaction['direction']>;
    transaction?: TransactionWithAccount;
};

export default function TransactionForm({
    accounts,
    directions,
    transaction,
}: Props) {
    const currencyForAccount = (accountId: string): string | null =>
        accounts.find((account) => String(account.id) === accountId)
            ?.currency_code ?? null;

    const initialAccountId = String(
        transaction?.account_id ?? accounts[0]?.id ?? '',
    );
    const initialCurrency =
        transaction?.account.currency_code ??
        currencyForAccount(initialAccountId);

    const form = useForm({
        account_id: initialAccountId,
        direction: transaction?.direction ?? directions[0] ?? 'debit',
        amount: transaction
            ? minorToDecimalString(
                  transaction.amount_minor,
                  initialCurrency ?? 'USD',
              )
            : '',
        description: transaction?.description ?? '',
        reference: transaction?.reference ?? '',
        notes: transaction?.notes ?? '',
        occurred_at: transaction
            ? toDateTimeLocalValue(transaction.occurred_at)
            : toDateTimeLocalValue(new Date().toISOString()),
    });

    const selectedCurrency = currencyForAccount(form.data.account_id);
    const errors = form.errors as Record<string, string | undefined>;
    const amountError = errors.amount ?? errors.amount_minor;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();

        const currency = currencyForAccount(form.data.account_id);

        if (currency === null) {
            form.setError('account_id', 'Select an account.');

            return;
        }

        const amountMinor = decimalStringToMinor(form.data.amount, currency);

        if (amountMinor === null) {
            form.setError('amount', 'Enter a valid amount.');

            return;
        }

        form.transform((data) => {
            const rest = { ...data };
            delete (rest as Record<string, unknown>).amount;

            return {
                ...rest,
                amount_minor: amountMinor,
                occurred_at: new Date(data.occurred_at).toISOString(),
            };
        });
        form.submit(transaction ? update(transaction) : store());
    }

    return (
        <form className="grid max-w-2xl gap-6" onSubmit={submit}>
            <TransactionAccountField form={form} accounts={accounts} />

            <FormField label="Direction" error={form.errors.direction}>
                <DirectionToggle
                    value={form.data.direction}
                    onValueChange={(value) =>
                        form.setData(
                            'direction',
                            value as Transaction['direction'],
                        )
                    }
                    options={directions.map((direction) => ({
                        value: direction,
                        label: humanize(direction),
                    }))}
                />
            </FormField>

            <TransactionAmountField
                value={form.data.amount}
                onChange={(value) => form.setData('amount', value)}
                error={amountError}
                currency={selectedCurrency}
            />

            <FormField label="Description" error={form.errors.description}>
                <Input
                    value={form.data.description}
                    onChange={(event) =>
                        form.setData('description', event.target.value)
                    }
                    required
                />
            </FormField>
            <FormField label="Reference" error={form.errors.reference}>
                <Input
                    value={form.data.reference}
                    onChange={(event) =>
                        form.setData('reference', event.target.value)
                    }
                />
            </FormField>
            <FormField label="Occurred at" error={form.errors.occurred_at}>
                <Input
                    type="datetime-local"
                    value={form.data.occurred_at}
                    onChange={(event) =>
                        form.setData('occurred_at', event.target.value)
                    }
                    required
                />
            </FormField>
            <FormField label="Notes" error={form.errors.notes}>
                <Textarea
                    value={form.data.notes}
                    onChange={(event) =>
                        form.setData('notes', event.target.value)
                    }
                />
            </FormField>
            <Button className="w-fit" disabled={form.processing}>
                {transaction ? 'Save changes' : 'Create transaction'}
            </Button>
        </form>
    );
}
