import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { DirectionBadge } from './direction-badge';

describe('DirectionBadge', () => {
    it('renders a credit badge', () => {
        render(<DirectionBadge direction="credit" />);

        expect(screen.getByText('Credit')).toBeInTheDocument();
    });

    it('renders a debit badge', () => {
        render(<DirectionBadge direction="debit" />);

        expect(screen.getByText('Debit')).toBeInTheDocument();
    });
});
