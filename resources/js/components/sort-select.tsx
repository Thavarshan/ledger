import { ArrowUpDown } from 'lucide-react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export type SortOption = {
    value: string;
    label: string;
};

type SortSelectProps = {
    name?: string;
    defaultValue: string;
    options: SortOption[];
};

export function SortSelect({
    name = 'sort',
    defaultValue,
    options,
}: SortSelectProps) {
    return (
        <Select name={name} defaultValue={defaultValue}>
            <SelectTrigger className="w-full sm:w-44">
                <ArrowUpDown className="text-muted-foreground" />
                <SelectValue placeholder="Sort by" />
            </SelectTrigger>
            <SelectContent>
                {options.map((option) => (
                    <SelectItem key={option.value} value={option.value}>
                        {option.label}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}
