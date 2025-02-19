<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\Traits\Filterable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The list of sortable fields for query filtering.
     *
     * @var array
     */
    protected static array $sortableFields = [
        'name',
        'email',
    ];

    /**
     * The list of simple searchable fields for query filtering.
     *
     * @var array
     */
    protected static array $simpleSearchableFields = [
        'name',
        'email',
    ];

    /**
     * The list of advanced searchable fields for query filtering.
     *
     * @var array
     */
    protected static array $advancedSearchableFields = [
        'name',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * With the details relationship.
     */
    protected $with = ['details'];

    /**
     * Get the password history records for the user.
     *
     * @return HasMany
     */
    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
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
                if (!empty($filters['groups'])) {
                    $query->$whereMethod(function ($q) use ($filters) {
                        $q->whereHas('groups', function ($subQ) use ($filters) {
                            $subQ->where('groups', 'LIKE', "%{$filters['groups']}%");
                        });
                    });
                }
                if (!empty($filters['roles'])) {
                    $query->$whereMethod(function ($q) use ($filters) {
                        $q->whereHas('roles', function ($subQ) use ($filters) {
                            $subQ->where('roles', 'LIKE', "%{$filters['roles']}%");
                        });
                    });
                }
                if (!empty($filters['permissions'])) {
                    $query->$whereMethod(function ($q) use ($filters) {
                        $q->whereHas('permissions', function ($subQ) use ($filters) {
                            $subQ->where('permissions', 'LIKE', "%{$filters['permissions']}%");
                        });
                    });
                }
            }
        );
    }

    /**
     * A model may have multiple groups.
     */
    public function groups(): MorphToMany
    {
        return $this->morphToMany(
            Group::class,
            'model',
            'model_has_groups',
            'model_id',
            'group_id'
        )->withTimestamps();
    }

    /**
     * A model may have multiple roles.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotRole
        );
    }

    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotPermission
        );
    }

    /**
     * Get the user's details relationship.
     *
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(UserDetail::class);
    }

    /**
     * Get the detail value for the given key.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @return string|null
     */
    public function getDetail(string $key, mixed $default = null): ?string
    {
        $detail = $this->details()->where('key', $key)->first();
        return $detail && is_scalar($detail->value) ? (string) $detail->value : $default;
    }

    /**
     * Set the detail value for the given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function setDetail(string $key, mixed $value): void
    {
        $this->details()->updateOrCreate(
            ['key' => $key],
            ['value' => is_scalar($value) ? (string) $value : null]
        );
    }

    /**
     * Delete a detail by key.
     *
     * @param  string  $key
     * @return void
     */
    public function deleteDetail(string $key): void
    {
        $this->details()->where('key', $key)->delete();
    }

    /**
     * Get the default group ID.
     *
     * @return int|null
    */
    public function getDefaultGroupIdAttribute(): ?int
    {
        $value = $this->getDetail('default_group_id');
        return $value !== null ? (int) $value : null;
    }

    /**
     * Get the current group ID.
     *
     * @return int|null
     */
    public function getCurrentGroupIdAttribute(): ?int
    {
        $value = $this->getDetail('current_group_id');
        return $value !== null ? (int) $value : null;
    }
}
