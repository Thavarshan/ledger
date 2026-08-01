import { useForm } from '@inertiajs/react';
import {
    store,
    update,
} from '@/actions/App/Http/Controllers/AccountController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export type Account = {
    id: number;
    name: string;
    account_type: string;
    account_holder_name: string | null;
    bank_name: string;
    bank_code: string | null;
    branch_name: string | null;
    branch_code: string | null;
    country_code: string;
    currency_code: string;
    account_number_last4: string | null;
    iban: string | null;
    swift_bic: string | null;
    routing_number: string | null;
    sort_code: string | null;
    notes: string | null;
    is_primary: boolean;
    is_active: boolean;
};

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
        <form onSubmit={submit} className="grid gap-6">
            <div className="grid gap-4 md:grid-cols-2">
                <Field label="Account name" error={form.errors.name}>
                    <Input
                        value={form.data.name}
                        onChange={(event) =>
                            form.setData('name', event.target.value)
                        }
                        required
                    />
                </Field>
                <Field label="Account type" error={form.errors.account_type}>
                    <Select
                        value={form.data.account_type}
                        onValueChange={(value) =>
                            form.setData('account_type', value)
                        }
                    >
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {accountTypes.map((type) => (
                                <SelectItem key={type} value={type}>
                                    {type.replace('_', ' ')}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </Field>
                <Field label="Bank name" error={form.errors.bank_name}>
                    <Input
                        value={form.data.bank_name}
                        onChange={(event) =>
                            form.setData('bank_name', event.target.value)
                        }
                        required
                    />
                </Field>
                <Field
                    label="Account holder"
                    error={form.errors.account_holder_name}
                >
                    <Input
                        value={form.data.account_holder_name}
                        onChange={(event) =>
                            form.setData(
                                'account_holder_name',
                                event.target.value,
                            )
                        }
                    />
                </Field>
                <Field label="Country code" error={form.errors.country_code}>
                    <Input
                        value={form.data.country_code}
                        onChange={(event) =>
                            form.setData(
                                'country_code',
                                event.target.value.toUpperCase(),
                            )
                        }
                        maxLength={2}
                        required
                    />
                </Field>
                <Field label="Currency" error={form.errors.currency_code}>
                    <Select
                        value={form.data.currency_code}
                        onValueChange={(value) =>
                            form.setData('currency_code', value)
                        }
                    >
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {currencies.map((currency) => (
                                <SelectItem key={currency} value={currency}>
                                    {currency}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </Field>
                <Field
                    label={
                        account
                            ? 'New account number (optional)'
                            : 'Account number'
                    }
                    error={form.errors.account_number}
                >
                    <Input
                        value={form.data.account_number}
                        onChange={(event) =>
                            form.setData('account_number', event.target.value)
                        }
                        required={!account}
                    />
                </Field>
                <Field label="SWIFT / BIC" error={form.errors.swift_bic}>
                    <Input
                        value={form.data.swift_bic}
                        onChange={(event) =>
                            form.setData(
                                'swift_bic',
                                event.target.value.toUpperCase(),
                            )
                        }
                    />
                </Field>
                <Field label="IBAN" error={form.errors.iban}>
                    <Input
                        value={form.data.iban}
                        onChange={(event) =>
                            form.setData('iban', event.target.value)
                        }
                    />
                </Field>
                <Field
                    label="Routing number"
                    error={form.errors.routing_number}
                >
                    <Input
                        value={form.data.routing_number}
                        onChange={(event) =>
                            form.setData('routing_number', event.target.value)
                        }
                    />
                </Field>
                <Field label="Sort code" error={form.errors.sort_code}>
                    <Input
                        value={form.data.sort_code}
                        onChange={(event) =>
                            form.setData('sort_code', event.target.value)
                        }
                    />
                </Field>
            </div>
            <div className="flex flex-wrap gap-6">
                <label className="flex items-center gap-2 text-sm">
                    <Checkbox
                        checked={form.data.is_primary}
                        onCheckedChange={(checked) =>
                            form.setData('is_primary', checked === true)
                        }
                    />
                    Primary account
                </label>
                <label className="flex items-center gap-2 text-sm">
                    <Checkbox
                        checked={form.data.is_active}
                        onCheckedChange={(checked) =>
                            form.setData('is_active', checked === true)
                        }
                    />
                    Active
                </label>
            </div>
            <Button className="w-fit" disabled={form.processing}>
                {account ? 'Save changes' : 'Create account'}
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
