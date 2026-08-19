import type { ReactNode } from 'react';
import {
    Field,
    FieldDescription,
    FieldError,
    FieldLabel,
} from '@/components/ui/field';

type FormFieldProps = {
    label: string;
    description?: string;
    error?: string;
    children: ReactNode;
};

export function FormField({
    label,
    description,
    error,
    children,
}: FormFieldProps) {
    return (
        <Field data-invalid={!!error}>
            <FieldLabel>{label}</FieldLabel>
            {children}
            {description && <FieldDescription>{description}</FieldDescription>}
            {error && <FieldError>{error}</FieldError>}
        </Field>
    );
}
