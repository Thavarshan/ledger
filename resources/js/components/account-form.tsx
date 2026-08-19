import { useForm } from '@inertiajs/react';
import {
    store,
    update,
} from '@/actions/App/Http/Controllers/AccountController';
import { AccountBankFields } from '@/components/account-bank-fields';
import { AccountIdentityFields } from '@/components/account-identity-fields';
import { AccountSensitiveFields } from '@/components/account-sensitive-fields';
import { AccountStatusFields } from '@/components/account-status-fields';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { FieldLegend, FieldSet } from '@/components/ui/field';
import { Textarea } from '@/components/ui/textarea';
import type { Account } from '@/types';

type AccountFormProps = {
    account?: Account;
    accountTypes: string[];
    currencies: string[];
};

export default function AccountForm({
    account,
    accountTypes,
    currencies,
}: AccountFormProps) {
    const form = useForm({
        name: account?.name ?? '',
        account_type: account?.account_type ?? accountTypes[0] ?? '',
        account_holder_name: account?.account_holder_name ?? '',
        bank_name: account?.bank_name ?? '',
        bank_code: account?.bank_code ?? '',
        branch_name: account?.branch_name ?? '',
        branch_code: account?.branch_code ?? '',
        country_code: account?.country_code ?? 'LK',
        currency_code: account?.currency_code ?? 'LKR',
        account_number: '',
        iban: '',
        swift_bic: account?.swift_bic ?? '',
        routing_number: '',
        sort_code: '',
        notes: account?.notes ?? '',
        is_primary: account?.is_primary ?? false,
        is_active: account?.is_active ?? true,
    });

    function submit(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (account) {
            form.transform((data) => {
                const {
                    account_number,
                    iban,
                    routing_number,
                    sort_code,
                    ...attributes
                } = data;

                return {
                    ...attributes,
                    ...(account_number !== '' && { account_number }),
                    ...(iban !== '' && { iban }),
                    ...(routing_number !== '' && { routing_number }),
                    ...(sort_code !== '' && { sort_code }),
                };
            });
        }

        form.submit(account ? update(account) : store());
    }

    return (
        <form onSubmit={submit} className="grid gap-8">
            <FieldSet>
                <FieldLegend>Identity</FieldLegend>
                <AccountIdentityFields
                    form={form}
                    accountTypes={accountTypes}
                    currencies={currencies}
                />
            </FieldSet>

            <FieldSet>
                <FieldLegend>Bank details</FieldLegend>
                <AccountBankFields form={form} />
            </FieldSet>

            <FieldSet>
                <FieldLegend>Sensitive identifiers</FieldLegend>
                <p className="-mt-2 mb-2 text-sm text-muted-foreground">
                    Stored encrypted and never shown in full once saved.
                </p>
                <AccountSensitiveFields form={form} account={account} />
            </FieldSet>

            <FieldSet>
                <FieldLegend>Status</FieldLegend>
                <AccountStatusFields form={form} />
            </FieldSet>

            <FormField label="Notes" error={form.errors.notes}>
                <Textarea
                    value={form.data.notes}
                    onChange={(event) =>
                        form.setData('notes', event.target.value)
                    }
                />
            </FormField>

            <Button className="w-fit" disabled={form.processing}>
                {account ? 'Save changes' : 'Create account'}
            </Button>
        </form>
    );
}
