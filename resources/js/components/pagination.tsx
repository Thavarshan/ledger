import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import type { Paginated } from '@/types';

export default function Pagination<T>({ items }: { items: Paginated<T> }) {
    if (items.meta.links.length <= 3) {
        return null;
    }

    return (
        <nav aria-label="Pagination" className="flex flex-wrap gap-2 p-3">
            {items.meta.links.map((link) => (
                <Button
                    asChild
                    disabled={!link.url}
                    key={link.label}
                    size="sm"
                    variant={link.active ? 'default' : 'outline'}
                >
                    {link.url ? (
                        <Link href={link.url} preserveScroll>
                            {link.label.replace(/&laquo;|&raquo;/g, '')}
                        </Link>
                    ) : (
                        <span>
                            {link.label.replace(/&laquo;|&raquo;/g, '')}
                        </span>
                    )}
                </Button>
            ))}
        </nav>
    );
}
