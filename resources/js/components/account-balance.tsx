import { Money } from '@/components/money';
import { cn } from '@/lib/utils';

type AccountBalanceProps = {
    balanceMinor: string;
    currency: string;
    className?: string;
};

export function AccountBalance({
    balanceMinor,
    currency,
    className,
}: AccountBalanceProps) {
    const isNegative = balanceMinor.startsWith('-');

    return (
        <Money
            amountMinor={balanceMinor}
            currency={currency}
            className={cn(
                'font-medium',
                isNegative && 'text-destructive',
                className,
            )}
        />
    );
}
