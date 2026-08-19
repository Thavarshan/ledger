import { ArrowDownRight, ArrowUpRight } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { Transaction } from '@/types';

const DIRECTION_CONFIG = {
    credit: {
        label: 'Credit',
        icon: ArrowUpRight,
        className:
            'border-emerald-600/20 bg-emerald-50 text-emerald-700 dark:border-emerald-400/30 dark:bg-emerald-950 dark:text-emerald-400',
    },
    debit: {
        label: 'Debit',
        icon: ArrowDownRight,
        className: 'border-border bg-muted text-foreground',
    },
} as const;

export function DirectionBadge({
    direction,
}: {
    direction: Transaction['direction'];
}) {
    const { label, icon: Icon, className } = DIRECTION_CONFIG[direction];

    return (
        <Badge variant="outline" className={cn('gap-1', className)}>
            <Icon className="size-3" />
            {label}
        </Badge>
    );
}
