import { Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { MoreHorizontal } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export type RowAction = {
    label: string;
    icon?: LucideIcon;
    href?: string;
    onSelect?: () => void;
    destructive?: boolean;
};

export function RowActionsMenu({ actions }: { actions: RowAction[] }) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon-sm">
                    <MoreHorizontal />
                    <span className="sr-only">Open actions</span>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                {actions.map((action) => {
                    const Icon = action.icon;

                    return (
                        <DropdownMenuItem
                            key={action.label}
                            variant={
                                action.destructive ? 'destructive' : 'default'
                            }
                            asChild={!!action.href}
                            onSelect={
                                action.href
                                    ? undefined
                                    : () => action.onSelect?.()
                            }
                        >
                            {action.href ? (
                                <Link href={action.href}>
                                    {Icon && <Icon />}
                                    {action.label}
                                </Link>
                            ) : (
                                <>
                                    {Icon && <Icon />}
                                    {action.label}
                                </>
                            )}
                        </DropdownMenuItem>
                    );
                })}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
