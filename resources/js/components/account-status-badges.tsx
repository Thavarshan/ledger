import { Badge } from '@/components/ui/badge';

type AccountStatusBadgesProps = {
    isPrimary: boolean;
    isActive: boolean;
};

export function AccountStatusBadges({
    isPrimary,
    isActive,
}: AccountStatusBadgesProps) {
    return (
        <div className="flex flex-wrap gap-1.5">
            {isPrimary && <Badge>Primary</Badge>}
            <Badge variant={isActive ? 'secondary' : 'outline'}>
                {isActive ? 'Active' : 'Inactive'}
            </Badge>
        </div>
    );
}
