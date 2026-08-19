import { describe, expect, it, vi } from 'vitest';

/**
 * jsdom implements neither of these, and several UI components touch them
 * while their module body is still evaluating. Stub them before the glob
 * imports.
 */
vi.stubGlobal(
    'matchMedia',
    vi.fn().mockImplementation((query: string) => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
    })),
);

vi.stubGlobal(
    'ResizeObserver',
    class {
        observe = vi.fn();
        unobserve = vi.fn();
        disconnect = vi.fn();
    },
);

describe('ui component registry', () => {
    const modules = import.meta.glob('../components/ui/*.tsx');
    const names = Object.keys(modules).sort();

    it('discovers the remaining component set', () => {
        expect(names.length).toBeGreaterThanOrEqual(20);
    });

    it.each(names)(
        '%s resolves and exports at least one binding',
        async (name) => {
            const mod = (await modules[name]()) as Record<string, unknown>;

            expect(Object.keys(mod).length).toBeGreaterThan(0);
        },
    );
});
