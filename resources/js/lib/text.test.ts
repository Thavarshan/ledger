import { describe, expect, it } from 'vitest';
import { humanize } from './text';

describe('humanize', () => {
    it('title-cases a snake_case value', () => {
        expect(humanize('fixed_deposit')).toBe('Fixed Deposit');
    });

    it('title-cases a single word', () => {
        expect(humanize('savings')).toBe('Savings');
    });

    it('title-cases a kebab-case value', () => {
        expect(humanize('fixed-deposit')).toBe('Fixed Deposit');
    });
});
