import type { InertiaFormProps } from '@inertiajs/react';
import { Checkbox } from '@/components/ui/checkbox';
import type { AccountFormData } from '@/types';

export function AccountStatusFields({
    form,
}: {
    form: InertiaFormProps<AccountFormData>;
}) {
    return (
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
    );
}
