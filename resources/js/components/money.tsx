import { formatMoney } from '@/lib/money';
import { cn } from '@/lib/utils';

type MoneyProps = {
    amountMinor: string;
    currency: string;
    locale?: string;
    className?: string;
};

export function Money({
    amountMinor,
    currency,
    locale,
    className,
}: MoneyProps) {
    return (
        <span className={cn('tabular-nums', className)}>
            {formatMoney(amountMinor, currency, locale)}
        </span>
    );
}
