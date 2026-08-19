import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { ConfirmDeleteDialog } from './confirm-delete-dialog';

describe('ConfirmDeleteDialog', () => {
    it('opens via its trigger and fires onConfirm when the action is clicked', async () => {
        const user = userEvent.setup();
        const onConfirm = vi.fn();

        render(
            <ConfirmDeleteDialog
                title="Delete account?"
                description="This cannot be undone."
                trigger={<button type="button">Open dialog</button>}
                onConfirm={onConfirm}
            />,
        );

        await user.click(screen.getByRole('button', { name: 'Open dialog' }));

        expect(
            screen.getByRole('heading', { name: 'Delete account?' }),
        ).toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Delete' }));

        expect(onConfirm).toHaveBeenCalledTimes(1);
    });

    it('supports fully controlled open state with no trigger', () => {
        const onOpenChange = vi.fn();

        render(
            <ConfirmDeleteDialog
                open
                onOpenChange={onOpenChange}
                title="Delete transaction?"
                description="This cannot be undone."
                onConfirm={vi.fn()}
            />,
        );

        expect(
            screen.getByRole('heading', { name: 'Delete transaction?' }),
        ).toBeInTheDocument();
    });
});
