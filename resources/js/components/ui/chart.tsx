import * as React from 'react';
import * as RechartsPrimitive from 'recharts';

import { cn } from '@/lib/utils';

export type ChartConfig = Record<
    string,
    {
        label?: React.ReactNode;
        color?: string;
    }
>;

type ChartContextValue = {
    config: ChartConfig;
};

const ChartContext = React.createContext<ChartContextValue | null>(null);

function useChart(): ChartContextValue {
    const context = React.useContext(ChartContext);

    if (!context) {
        throw new Error('Chart components must be used inside ChartContainer.');
    }

    return context;
}

export function ChartContainer({
    config,
    className,
    children,
}: React.ComponentProps<'div'> & { config: ChartConfig }) {
    const id = React.useId();

    return (
        <ChartContext.Provider value={{ config }}>
            <div
                data-chart={id}
                className={cn(
                    'flex aspect-video justify-center text-xs [&_.recharts-cartesian-axis-tick_text]:fill-muted-foreground [&_.recharts-cartesian-grid_line]:stroke-border/50 [&_.recharts-curve.recharts-tooltip-cursor]:stroke-border [&_.recharts-dot[stroke="#fff"]]:stroke-transparent [&_.recharts-layer]:outline-hidden [&_.recharts-polar-grid_[stroke="#ccc"]]:stroke-border [&_.recharts-radial-bar-background-sector]:fill-muted [&_.recharts-rectangle.recharts-tooltip-cursor]:fill-muted [&_.recharts-reference-line_[stroke="#ccc"]]:stroke-border [&_.recharts-sector[stroke="#fff"]]:stroke-transparent [&_.recharts-sector]:outline-hidden [&_.recharts-surface]:outline-hidden',
                    className,
                )}
            >
                <ChartStyle config={config} id={id} />
                <RechartsPrimitive.ResponsiveContainer>
                    {children}
                </RechartsPrimitive.ResponsiveContainer>
            </div>
        </ChartContext.Provider>
    );
}

function ChartStyle({ config, id }: { config: ChartConfig; id: string }) {
    const colorVariables = Object.entries(config).filter(
        ([, value]) => value.color,
    );

    if (colorVariables.length === 0) {
        return null;
    }

    return (
        <style
            dangerouslySetInnerHTML={{
                __html: colorVariables
                    .map(
                        ([key, value]) =>
                            `[data-chart="${id}"] { --color-${key}: ${value.color}; }`,
                    )
                    .join('\n'),
            }}
        />
    );
}

type ChartTooltipItem = {
    name?: string | number;
    dataKey?: string | number;
    value?: string | number;
    color?: string;
    payload?: Record<string, unknown>;
};

type ChartTooltipContentProps = {
    active?: boolean;
    payload?: ChartTooltipItem[];
    label?: React.ReactNode;
    formatter?: (
        value: number,
        name: string,
        item: ChartTooltipItem,
    ) => React.ReactNode;
};

export function ChartTooltipContent({
    active,
    payload,
    label,
    formatter,
}: ChartTooltipContentProps) {
    const { config } = useChart();

    if (!active || !payload?.length) {
        return null;
    }

    return (
        <div className="grid min-w-32 gap-1.5 rounded-lg border bg-background px-3 py-2 text-xs shadow-xl">
            <div className="font-medium">{label}</div>
            <div className="grid gap-1">
                {payload.map((item) => {
                    const name = String(item.name ?? item.dataKey ?? 'value');
                    const value = Number(item.value ?? 0);
                    const labelValue = config[name]?.label ?? name;

                    return (
                        <div
                            key={name}
                            className="flex items-center justify-between gap-4"
                        >
                            <span className="flex items-center gap-1.5 text-muted-foreground">
                                <span
                                    className="size-2 rounded-xs"
                                    style={{
                                        backgroundColor:
                                            item.color ??
                                            `var(--color-${name})`,
                                    }}
                                />
                                {labelValue}
                            </span>
                            <span className="font-mono font-medium tabular-nums">
                                {formatter ? formatter(value, name, item) : value}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
