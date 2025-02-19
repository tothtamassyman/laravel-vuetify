<?php

namespace App\Models;

use App\Http\Requests\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * The Group model represents application groupss that are assigned to users.
 *
 * Groups define access control rules for different resources within the application.
 * They can include conditions and inverted logic for flexible access management.
 */
class Group extends Model
{
    use HasFactory, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The list of sortable fields for query filtering.
     *
     * @var array
     */
    protected static array $sortableFields = [
        'name',
        'description',
    ];

    /**
     * The list of simple searchable fields for query filtering.
     *
     * @var array
     */
    protected static array $simpleSearchableFields = [
        'name',
        'description',
    ];

    /**
     * The list of advanced searchable fields for query filtering.
     *
     * @var array
     */
    protected static array $advancedSearchableFields = [
        'name',
        'description',
    ];

    /**
     * Applies filtering conditions to the query based on the provided criteria.
     * Supports search terms, sorting, and conditional filters.
     * The condition parameter can be 'and' or 'or' for combining filters.
     * Returns the modified query builder instance for further chaining.
     *
     * @param  Builder  $query
     * @param  array  $filters
     * @param  string  $condition
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters, string $condition = 'and'): Builder
    {
        return $query->applyFilters(
            $filters,
            self::$simpleSearchableFields,
            self::$advancedSearchableFields,
            self::$sortableFields,
            $condition,
            // Custom clauses specific to the Permission model
            function ($query, $filters, $whereMethod) {
                if (!empty($filters['users'])) {
                    $query->$whereMethod(function ($q) use ($filters) {
                        $q->whereHas('users', function ($subQ) use ($filters) {
                            $subQ->where('users', 'LIKE', "%{$filters['users']}%");
                        });
                    });
                }
            }
        );
    }

    /**
     * A model may have multiple direct users.
     *
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_groups',
            'group_id',
            'model_id'
        )->withTimestamps();
    }
}
