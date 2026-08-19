import { describe, expect, it } from 'vitest';
import { formatDate, formatDateTime, toDateTimeLocalValue } from './date';

describe('formatDate', () => {
    it('formats an ISO string as a medium date', () => {
        expect(formatDate('2026-08-01T10:30:00Z', 'en-US')).toBe('Aug 1, 2026');
    });
});

describe('formatDateTime', () => {
    it('formats an ISO string as a medium date with a short time', () => {
        expect(formatDateTime('2026-08-01T10:30:00Z', 'en-US')).toContain(
            'Aug 1, 2026',
        );
    });
});

describe('toDateTimeLocalValue', () => {
    it('produces a value usable by <input type="datetime-local">', () => {
        const result = toDateTimeLocalValue('2026-08-01T10:30:00Z');

        expect(result).toMatch(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/);
    });
});
