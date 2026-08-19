import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import type { Paginated } from '@/types';

export default function Pagination<T>({ items }: { items: Paginated<T> }) {
    const { current_page, per_page, total } = items.meta;

    if (total === 0) {
        return null;
    }

    const firstItem = (current_page - 1) * per_page + 1;
    const lastItem = Math.min(current_page * per_page, total);

    return (
        <div className="flex flex-wrap items-center justify-between gap-2 p-3">
            <p className="text-sm text-muted-foreground">
                Showing {firstItem}–{lastItem} of {total}
            </p>
            {items.meta.links.length > 3 && (
                <nav aria-label="Pagination" className="flex flex-wrap gap-2">
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
            )}
        </div>
    );
}
