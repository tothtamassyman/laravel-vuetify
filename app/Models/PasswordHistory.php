<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PasswordHistory Model
 *
 * Represents a user's password history to enforce password reuse restrictions.
 * This model ensures that previously used passwords are stored and can be
 * referenced to prevent a user from reusing them.
 */
class PasswordHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * This allows only specific fields to be mass assigned, preventing
     * unintended attribute manipulation during model creation or updates.
     */
    protected $fillable = [
        'user_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * These attributes are hidden to prevent sensitive data, such as
     * hashed passwords, from being exposed in API responses or JSON serializations.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * Get the user that owns this password history record.
     *
     * Defines a belongs-to relationship, where each password history
     * entry is associated with a specific user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
