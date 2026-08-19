import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { AccountBalance } from './account-balance';

describe('AccountBalance', () => {
    it('formats a positive balance without a destructive color', () => {
        render(<AccountBalance balanceMinor="150000" currency="USD" />);

        const el = screen.getByText('$1,500.00');
        expect(el).toBeInTheDocument();
        expect(el).not.toHaveClass('text-destructive');
    });

    it('formats a negative balance with a destructive color', () => {
        render(<AccountBalance balanceMinor="-2500" currency="USD" />);

        const el = screen.getByText('-$25.00');
        expect(el).toBeInTheDocument();
        expect(el).toHaveClass('text-destructive');
    });
});
