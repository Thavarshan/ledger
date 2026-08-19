import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';

type DirectionToggleOption = {
    value: string;
    label: string;
};

type DirectionToggleProps = {
    value: string;
    onValueChange: (value: string) => void;
    options: DirectionToggleOption[];
};

export function DirectionToggle({
    value,
    onValueChange,
    options,
}: DirectionToggleProps) {
    return (
        <ToggleGroup
            type="single"
            variant="outline"
            value={value}
            onValueChange={(next) => {
                if (next) {
                    onValueChange(next);
                }
            }}
        >
            {options.map((option) => (
                <ToggleGroupItem
                    key={option.value}
                    value={option.value}
                    className="flex-1"
                >
                    {option.label}
                </ToggleGroupItem>
            ))}
        </ToggleGroup>
    );
}
