<?php

namespace App\Models;

use App\Http\Requests\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * The Permission model represents application permissions that are assigned to roles and users.
 *
 * Permissions define access control rules for different resources within the application.
 * They can include conditions and inverted logic for flexible access management.
 */
class Role extends SpatieRole
{
    use HasFactory, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'guard_name',
    ];

    /**
     * The list of sortable fields for query filtering.
     *
     * @var array
     */
    protected static array $sortableFields = [
        'name',
        'description',
        'guard_name',
    ];

    /**
     * The list of simple searchable fields for query filtering.
     *
     * @var array
     */
    protected static array $simpleSearchableFields = [
        'name',
        'description',
        'guard_name',
    ];

    /**
     * The list of advanced searchable fields for query filtering.
     *
     * @var array
     */
    protected static array $advancedSearchableFields = [
        'name',
        'description',
        'guard_name',
    ];

    /**
     * Get the guard name for the permission or return the default.
     *
     * @return string
     */
    public function getGuardNameAttribute(): string
    {
        return $this->attributes['guard_name'] ?? config('auth.defaults.guard');
    }

    /**
     * Set the guard name for the permission.
     *
     * @param  string  $value
     * @return void
     */
    public function setGuardNameAttribute(string $value): void
    {
        $this->attributes['guard_name'] = $value;
    }

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
                if (!empty($filters['permissions'])) {
                    $query->$whereMethod(function ($q) use ($filters) {
                        $q->whereHas('permissions', function ($subQ) use ($filters) {
                            $subQ->where('name', 'LIKE', "%{$filters['permissions']}%");
                        });
                    });
                }
            }
        );
    }

    /**
     * The boot function to handle model events.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(fn($permission) => $permission->setGuardNameIfNotExists());
    }

    /**
     * Ensure the guard_name attribute is set with a valid value.
     *
     * @return void
     */
    public function setGuardNameIfNotExists(): void
    {
        $this->guard_name = ($this->guard_name && in_array($this->guard_name, array_keys(config('auth.guards'))))
            ? $this->guard_name
            : config('auth.defaults.guard');
    }

    /**
     * A model belongs to a group.
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * A model belongs to a User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the permissions that have the permission.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
