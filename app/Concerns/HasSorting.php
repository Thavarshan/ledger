<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Adds a configured, safe sort scope to an Eloquent model.
 *
 * @phpstan-require-extends Model
 */
trait HasSorting
{
    /**
     * Get the public sort keys mapped to database columns.
     *
     * @return array<string, string>
     */
    abstract protected function sortableColumns(): array;

    /**
     * Get the default column used when no public sort is selected.
     */
    protected function defaultSortColumn(): string
    {
        return 'created_at';
    }

    /**
     * Order by an approved column, newest first by default.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function sorted(Builder $query, ?string $value): void
    {
        if ($value === null || $value === '') {
            $query->orderByDesc($this->defaultSortColumn())->orderByDesc($this->getKeyName());

            return;
        }

        [$key, $direction] = array_pad(explode(':', $value, 2), 2, null);
        $column = $key === null ? null : $this->sortableColumns()[Str::lower($key)] ?? null;

        if ($column === null) {
            $query->orderByDesc($this->defaultSortColumn())->orderByDesc($this->getKeyName());

            return;
        }

        $direction = match (Str::lower((string) $direction)) {
            'desc' => 'desc',
            default => 'asc',
        };

        $query->orderBy($column, $direction)->orderByDesc($this->getKeyName());
    }
}
