import { describe, expect, it } from 'vitest';
import { toLocalDateTime } from './transaction-form';

describe('toLocalDateTime', () => {
    it('converts an ISO timestamp into the value accepted by datetime-local inputs', () => {
        expect(toLocalDateTime('2026-08-01T10:15:00.000Z')).toMatch(
            /^2026-08-01T\d{2}:\d{2}$/,
        );
    });
});
