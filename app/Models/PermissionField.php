<?php

namespace App\Models;

use App\Http\Requests\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionField extends Model
{
    use HasFactory, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permission_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field',
    ];

    /**
     * The permissions that belong to the field.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_field_pivot', 'field_id', 'permission_id');
    }
}