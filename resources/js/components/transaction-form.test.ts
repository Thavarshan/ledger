import { describe, expect, it } from 'vitest';
import { toDateTimeLocalValue } from '@/lib/date';

describe('toDateTimeLocalValue', () => {
    it('converts an ISO timestamp into the value accepted by datetime-local inputs', () => {
        expect(toDateTimeLocalValue('2026-08-01T10:15:00.000Z')).toMatch(
            /^2026-08-01T\d{2}:\d{2}$/,
        );
    });
});
