import type { InertiaFormProps } from '@inertiajs/react';
import { FormField } from '@/components/form-field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { humanize } from '@/lib/text';
import type { AccountFormData } from '@/types';

type AccountIdentityFieldsProps = {
    form: InertiaFormProps<AccountFormData>;
    accountTypes: string[];
    currencies: string[];
};

export function AccountIdentityFields({
    form,
    accountTypes,
    currencies,
}: AccountIdentityFieldsProps) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <FormField label="Account name" error={form.errors.name}>
                <Input
                    value={form.data.name}
                    onChange={(event) =>
                        form.setData('name', event.target.value)
                    }
                    required
                />
            </FormField>
            <FormField label="Account type" error={form.errors.account_type}>
                <Select
                    value={form.data.account_type}
                    onValueChange={(value) =>
                        form.setData('account_type', value)
                    }
                >
                    <SelectTrigger className="w-full">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {accountTypes.map((type) => (
                            <SelectItem key={type} value={type}>
                                {humanize(type)}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </FormField>
            <FormField
                label="Account holder"
                error={form.errors.account_holder_name}
            >
                <Input
                    value={form.data.account_holder_name}
                    onChange={(event) =>
                        form.setData('account_holder_name', event.target.value)
                    }
                />
            </FormField>
            <FormField label="Currency" error={form.errors.currency_code}>
                <Select
                    value={form.data.currency_code}
                    onValueChange={(value) =>
                        form.setData('currency_code', value)
                    }
                >
                    <SelectTrigger className="w-full">
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
            </FormField>
            <FormField label="Country code" error={form.errors.country_code}>
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
            </FormField>
        </div>
    );
}
