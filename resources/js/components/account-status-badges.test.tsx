import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { AccountStatusBadges } from './account-status-badges';

describe('AccountStatusBadges', () => {
    it('shows Primary and Active for a primary, active account', () => {
        render(<AccountStatusBadges isPrimary isActive />);

        expect(screen.getByText('Primary')).toBeInTheDocument();
        expect(screen.getByText('Active')).toBeInTheDocument();
    });

    it('omits Primary and shows Inactive for a non-primary, inactive account', () => {
        render(<AccountStatusBadges isPrimary={false} isActive={false} />);

        expect(screen.queryByText('Primary')).not.toBeInTheDocument();
        expect(screen.getByText('Inactive')).toBeInTheDocument();
    });
});
