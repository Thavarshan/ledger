import type { InertiaFormProps } from '@inertiajs/react';
import { FormField } from '@/components/form-field';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AccountOption, TransactionFormData } from '@/types';

type TransactionAccountFieldProps = {
    form: InertiaFormProps<TransactionFormData>;
    accounts: AccountOption[];
};

export function TransactionAccountField({
    form,
    accounts,
}: TransactionAccountFieldProps) {
    return (
        <FormField label="Account" error={form.errors.account_id}>
            <Select
                value={form.data.account_id}
                onValueChange={(value) => form.setData('account_id', value)}
            >
                <SelectTrigger className="w-full">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    {accounts.map((account) => (
                        <SelectItem key={account.id} value={String(account.id)}>
                            {account.name} ({account.currency_code})
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </FormField>
    );
}
