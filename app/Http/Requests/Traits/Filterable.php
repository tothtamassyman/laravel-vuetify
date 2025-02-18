<?php

namespace App\Http\Requests\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply general filters to an Eloquent query.
     *
     * @param  Builder  $query
     * @param  array  $filters
     * @param  array  $simpleSearchableFields
     * @param  array  $advancedSearchableFields
     * @param  array  $sortableFields
     * @param  string  $condition
     * @param  callable|null  $customClauses
     * @return Builder
     */
    public function scopeApplyFilters(
        Builder $query,
        array $filters,
        array $simpleSearchableFields,
        array $advancedSearchableFields,
        array $sortableFields,
        string $condition = 'and',
        ?callable $customClauses = null
    ): Builder {
        $whereMethod = strtolower($condition) === 'or' ? 'orWhere' : 'where';

        // Simple search across multiple fields
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters, $simpleSearchableFields) {
                foreach ($simpleSearchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%$filters[search]%");
                }
            });
        }

        // Advanced search based on field filters including custom clauses
        $query->where(function ($q) use ($filters, $advancedSearchableFields, $whereMethod, $customClauses) {
            foreach ($advancedSearchableFields as $field) {
                if (!empty($filters[$field])) {
                    $q->$whereMethod($field, 'LIKE', "%$filters[$field]%");
                }
            }

            // Apply the custom clauses using the same condition logic
            if ($customClauses) {
                $customClauses($q, $filters, $whereMethod);
            }
        });

        // Sorting logic
        if (!empty($filters['sort_by']) && in_array($filters['sort_by'], $sortableFields)) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        }

        return $query;
    }
}
