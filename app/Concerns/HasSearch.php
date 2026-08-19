<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Adds a configured free-text search scope to an Eloquent model.
 *
 * @phpstan-require-extends Model
 */
trait HasSearch
{
    /**
     * Get the columns that may be searched.
     *
     * @return list<string>
     */
    abstract protected function searchableColumns(): array;

    /**
     * Search the model's configured non-sensitive columns.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        $columns = $this->searchableColumns();

        if ($term === null || $term === '' || $columns === []) {
            return;
        }

        $query->where(function (Builder $query) use ($columns, $term): void {
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $query->whereLike($column, "%{$term}%");

                    continue;
                }

                $query->orWhereLike($column, "%{$term}%");
            }
        });
    }
}
