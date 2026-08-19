import { Money } from '@/components/money';
import { cn } from '@/lib/utils';
import type { Transaction } from '@/types';

type TransactionAmountProps = {
    amountMinor: string;
    currency: string;
    direction: Transaction['direction'];
    className?: string;
};

export function TransactionAmount({
    amountMinor,
    currency,
    direction,
    className,
}: TransactionAmountProps) {
    const isCredit = direction === 'credit';

    return (
        <span
            className={cn(
                'font-medium',
                isCredit
                    ? 'text-emerald-700 dark:text-emerald-400'
                    : 'text-foreground',
                className,
            )}
        >
            {isCredit ? '+' : '−'}{' '}
            <Money amountMinor={amountMinor} currency={currency} />
        </span>
    );
}
