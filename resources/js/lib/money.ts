const formatterCache = new Map<string, Intl.NumberFormat>();

function currencyFormatter(
    currency: string,
    locale?: string,
): Intl.NumberFormat {
    const key = `${locale ?? ''}:${currency}`;
    let formatter = formatterCache.get(key);

    if (!formatter) {
        formatter = new Intl.NumberFormat(locale, {
            style: 'currency',
            currency,
            currencyDisplay: 'narrowSymbol',
        });
        formatterCache.set(key, formatter);
    }

    return formatter;
}

export function minorUnitExponent(currency: string, locale?: string): number {
    return (
        currencyFormatter(currency, locale).resolvedOptions()
            .maximumFractionDigits ?? 0
    );
}

export function decimalStringToMinor(
    amount: string,
    currency: string,
    locale?: string,
): string | null {
    const normalized = amount.trim();

    if (!/^\d+(?:\.\d+)?$/.test(normalized)) {
        return null;
    }

    const exponent = minorUnitExponent(currency, locale);
    const [whole, fraction = ''] = normalized.split('.');
    const scale = 10n ** BigInt(exponent);
    const fractionForMinor = fraction.slice(0, exponent).padEnd(exponent, '0');
    let minor = BigInt(whole) * scale + BigInt(fractionForMinor || '0');

    if (fraction.length > exponent && fraction[exponent] >= '5') {
        minor += 1n;
    }

    return minor.toString();
}

export function minorToDecimalString(
    amountMinor: string | bigint,
    currency: string,
    locale?: string,
): string {
    const exponent = minorUnitExponent(currency, locale);
    const minor = BigInt(amountMinor);
    const negative = minor < 0n;
    const absolute = negative ? -minor : minor;

    if (exponent === 0) {
        return `${negative ? '-' : ''}${absolute}`;
    }

    const scale = 10n ** BigInt(exponent);
    const whole = absolute / scale;
    const fraction = (absolute % scale).toString().padStart(exponent, '0');

    return `${negative ? '-' : ''}${whole}.${fraction}`;
}

export function formatMoney(
    amountMinor: string | bigint,
    currency: string,
    locale?: string,
): string {
    const decimal = minorToDecimalString(amountMinor, currency, locale);
    const negative = decimal.startsWith('-');
    const unsigned = negative ? decimal.slice(1) : decimal;
    const [whole, fraction] = unsigned.split('.');
    const formatter = currencyFormatter(currency, locale);
    const parts = formatter.formatToParts(negative ? -1 : 1);
    const wholeFormatted = new Intl.NumberFormat(locale, {
        maximumFractionDigits: 0,
        useGrouping: true,
    }).format(BigInt(whole));
    const fractionFormatted = fraction
        ? new Intl.NumberFormat(locale, {
              maximumFractionDigits: fraction.length,
              minimumFractionDigits: fraction.length,
              useGrouping: false,
          })
              .formatToParts(Number(`0.${fraction}`))
              .find((part) => part.type === 'fraction')?.value
        : undefined;
    let integerReplaced = false;

    return parts
        .map((part) => {
            if (part.type === 'integer' && !integerReplaced) {
                integerReplaced = true;

                return wholeFormatted;
            }

            if (part.type === 'fraction' && fractionFormatted !== undefined) {
                return fractionFormatted;
            }

            return part.value;
        })
        .join('');
}
