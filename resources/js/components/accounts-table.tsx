import { Link, router } from '@inertiajs/react';
import { Landmark, SquarePen, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    destroy,
    edit,
    show,
} from '@/actions/App/Http/Controllers/AccountController';
import { AccountBalance } from '@/components/account-balance';
import { AccountStatusBadges } from '@/components/account-status-badges';
import { ConfirmDeleteDialog } from '@/components/confirm-delete-dialog';
import { RowActionsMenu } from '@/components/row-actions-menu';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import type { AccountListItem } from '@/types';

export function AccountsTable({ accounts }: { accounts: AccountListItem[] }) {
    const [pendingDeleteId, setPendingDeleteId] = useState<number | null>(null);
    const pendingDeleteAccount = accounts.find(
        (account) => account.id === pendingDeleteId,
    );

    if (accounts.length === 0) {
        return (
            <Empty>
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <Landmark />
                    </EmptyMedia>
                    <EmptyTitle>No accounts yet</EmptyTitle>
                    <EmptyDescription>
                        Add a bank account to start recording transactions.
                    </EmptyDescription>
                </EmptyHeader>
            </Empty>
        );
    }

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Account</TableHead>
                        <TableHead>Bank</TableHead>
                        <TableHead>Currency</TableHead>
                        <TableHead className="text-right">Balance</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className="w-px" />
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {accounts.map((account) => (
                        <TableRow key={account.id}>
                            <TableCell>
                                <Link
                                    className="font-medium hover:underline"
                                    href={show(account)}
                                >
                                    {account.name}
                                </Link>
                                {account.account_number_last4 && (
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <div className="cursor-default text-muted-foreground">
                                                ••••{' '}
                                                {account.account_number_last4}
                                            </div>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            Full number stored encrypted
                                        </TooltipContent>
                                    </Tooltip>
                                )}
                            </TableCell>
                            <TableCell>{account.bank_name}</TableCell>
                            <TableCell>{account.currency_code}</TableCell>
                            <TableCell className="text-right">
                                {account.balance_minor !== undefined ? (
                                    <AccountBalance
                                        balanceMinor={account.balance_minor}
                                        currency={account.currency_code}
                                    />
                                ) : (
                                    <span className="text-muted-foreground">
                                        —
                                    </span>
                                )}
                            </TableCell>
                            <TableCell>
                                <AccountStatusBadges
                                    isPrimary={account.is_primary}
                                    isActive={account.is_active}
                                />
                            </TableCell>
                            <TableCell>
                                <RowActionsMenu
                                    actions={[
                                        {
                                            label: 'Edit',
                                            icon: SquarePen,
                                            href: edit(account).url,
                                        },
                                        {
                                            label: 'Delete',
                                            icon: Trash2,
                                            destructive: true,
                                            onSelect: () =>
                                                setPendingDeleteId(account.id),
                                        },
                                    ]}
                                />
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <ConfirmDeleteDialog
                open={pendingDeleteId !== null}
                onOpenChange={(open) => !open && setPendingDeleteId(null)}
                title="Delete account?"
                description={
                    pendingDeleteAccount
                        ? `This permanently deletes "${pendingDeleteAccount.name}". This cannot be undone.`
                        : ''
                }
                onConfirm={() => {
                    if (pendingDeleteAccount) {
                        router.delete(destroy(pendingDeleteAccount).url);
                    }

                    setPendingDeleteId(null);
                }}
            />
        </>
    );
}
