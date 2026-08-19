import { FormField } from '@/components/form-field';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupInput,
    InputGroupText,
} from '@/components/ui/input-group';
import { minorUnitExponent } from '@/lib/money';

type TransactionAmountFieldProps = {
    value: string;
    onChange: (value: string) => void;
    error?: string;
    currency: string | null;
};

export function TransactionAmountField({
    value,
    onChange,
    error,
    currency,
}: TransactionAmountFieldProps) {
    const step =
        currency === null
            ? undefined
            : (10 ** -minorUnitExponent(currency)).toString();

    return (
        <FormField label="Amount" error={error}>
            <InputGroup>
                <InputGroupAddon>
                    <InputGroupText>{currency ?? '—'}</InputGroupText>
                </InputGroupAddon>
                <InputGroupInput
                    inputMode="decimal"
                    type="number"
                    step={step}
                    min={step}
                    disabled={currency === null}
                    value={value}
                    onChange={(event) => onChange(event.target.value)}
                    required
                />
            </InputGroup>
        </FormField>
    );
}
