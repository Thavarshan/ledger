import { Badge } from '@/components/ui/badge';
import { humanize } from '@/lib/text';

export function AccountTypeBadge({ type }: { type: string }) {
    return <Badge variant="outline">{humanize(type)}</Badge>;
}
