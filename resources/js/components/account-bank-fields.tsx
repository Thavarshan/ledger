import type { InertiaFormProps } from '@inertiajs/react';
import { FormField } from '@/components/form-field';
import { Input } from '@/components/ui/input';
import type { AccountFormData } from '@/types';

export function AccountBankFields({
    form,
}: {
    form: InertiaFormProps<AccountFormData>;
}) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            <FormField label="Bank name" error={form.errors.bank_name}>
                <Input
                    value={form.data.bank_name}
                    onChange={(event) =>
                        form.setData('bank_name', event.target.value)
                    }
                    required
                />
            </FormField>
            <FormField label="Bank code" error={form.errors.bank_code}>
                <Input
                    value={form.data.bank_code}
                    onChange={(event) =>
                        form.setData('bank_code', event.target.value)
                    }
                />
            </FormField>
            <FormField label="Branch name" error={form.errors.branch_name}>
                <Input
                    value={form.data.branch_name}
                    onChange={(event) =>
                        form.setData('branch_name', event.target.value)
                    }
                />
            </FormField>
            <FormField label="Branch code" error={form.errors.branch_code}>
                <Input
                    value={form.data.branch_code}
                    onChange={(event) =>
                        form.setData('branch_code', event.target.value)
                    }
                />
            </FormField>
        </div>
    );
}
