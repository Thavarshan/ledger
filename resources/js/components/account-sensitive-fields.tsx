import type { InertiaFormProps } from '@inertiajs/react';
import { FormField } from '@/components/form-field';
import { Input } from '@/components/ui/input';
import type { Account, AccountFormData } from '@/types';

type AccountSensitiveFieldsProps = {
    form: InertiaFormProps<AccountFormData>;
    account?: Account;
};

export function AccountSensitiveFields({
    form,
    account,
}: AccountSensitiveFieldsProps) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <FormField
                label={
                    account ? 'New account number (optional)' : 'Account number'
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
            </FormField>
            <FormField label="SWIFT / BIC" error={form.errors.swift_bic}>
                <Input
                    value={form.data.swift_bic}
                    onChange={(event) =>
                        form.setData(
                            'swift_bic',
                            event.target.value.toUpperCase(),
                        )
                    }
                />
            </FormField>
            <FormField label="IBAN" error={form.errors.iban}>
                <Input
                    value={form.data.iban}
                    onChange={(event) =>
                        form.setData('iban', event.target.value)
                    }
                />
            </FormField>
            <FormField
                label="Routing number"
                error={form.errors.routing_number}
            >
                <Input
                    value={form.data.routing_number}
                    onChange={(event) =>
                        form.setData('routing_number', event.target.value)
                    }
                />
            </FormField>
            <FormField label="Sort code" error={form.errors.sort_code}>
                <Input
                    value={form.data.sort_code}
                    onChange={(event) =>
                        form.setData('sort_code', event.target.value)
                    }
                />
            </FormField>
        </div>
    );
}
