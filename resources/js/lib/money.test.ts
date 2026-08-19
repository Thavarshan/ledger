import { describe, expect, it } from 'vitest';
import {
    decimalStringToMinor,
    formatMoney,
    minorToDecimalString,
    minorUnitExponent,
} from './money';

describe('minorUnitExponent', () => {
    it('resolves currency-specific decimal places', () => {
        expect(minorUnitExponent('USD', 'en-US')).toBe(2);
        expect(minorUnitExponent('JPY', 'en-US')).toBe(0);
        expect(minorUnitExponent('BHD', 'en-US')).toBe(3);
    });
});

describe('decimalStringToMinor', () => {
    it('converts zero-, two-, and three-decimal currencies exactly', () => {
        expect(decimalStringToMinor('1000', 'JPY', 'en-US')).toBe('1000');
        expect(decimalStringToMinor('1500', 'USD', 'en-US')).toBe('150000');
        expect(decimalStringToMinor('1500.5', 'BHD', 'en-US')).toBe('1500500');
    });

    it('rounds excess fractional digits half-up', () => {
        expect(decimalStringToMinor('1.005', 'USD', 'en-US')).toBe('101');
        expect(decimalStringToMinor('1.004', 'USD', 'en-US')).toBe('100');
    });

    it('supports values beyond Number safe integer limits', () => {
        expect(
            decimalStringToMinor('90071992547409931234.56', 'USD', 'en-US'),
        ).toBe('9007199254740993123456');
    });

    it('rejects empty and invalid input', () => {
        expect(decimalStringToMinor('', 'USD', 'en-US')).toBeNull();
        expect(decimalStringToMinor('1.2.3', 'USD', 'en-US')).toBeNull();
        expect(decimalStringToMinor('-1', 'USD', 'en-US')).toBeNull();
    });
});

describe('minorToDecimalString', () => {
    it('preserves exact currency-specific fractional digits', () => {
        expect(minorToDecimalString('150000', 'USD', 'en-US')).toBe('1500.00');
        expect(minorToDecimalString('1000', 'JPY', 'en-US')).toBe('1000');
        expect(minorToDecimalString('1500500', 'BHD', 'en-US')).toBe(
            '1500.500',
        );
    });

    it('handles negative values and large values', () => {
        expect(minorToDecimalString('-105', 'USD', 'en-US')).toBe('-1.05');
        expect(
            minorToDecimalString('9007199254740993123456', 'USD', 'en-US'),
        ).toBe('90071992547409931234.56');
    });
});

describe('formatMoney', () => {
    it('formats exact values without converting the whole amount to Number', () => {
        expect(formatMoney('150000', 'USD', 'en-US')).toBe('$1,500.00');
        expect(formatMoney('1000', 'JPY', 'en-US')).toBe('¥1,000');
        expect(formatMoney('1500500', 'BHD', 'en-US')).toContain('1,500.500');
        expect(formatMoney('-105', 'USD', 'en-US')).toBe('-$1.05');
    });
});
