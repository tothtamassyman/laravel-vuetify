<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * Get the user that owns the detail.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The "booted" method of the model.
     *
     * This method is used to set up model event hooks, allowing us to trigger
     * certain actions during specific Eloquent model events.
     * In this case, when a `UserDetail` model is being created (`creating` event),
     * we ensure the associated `User` model's `updated_at` timestamp is refreshed
     * to reflect the change by calling `touch` on the `User` model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($userDetail) {
            static::checkIfDetailsExists($userDetail, false);
        });

        static::updating(function ($userDetail) {
            static::checkIfDetailsExists($userDetail, true);
        });
    }

    /**
     * Check if the user detail exists.
     *
     * @param $userDetail
     * @param  bool  $checkId
     * @return void
     * @throws ValidationException
     */
    protected static function checkIfDetailsExists($userDetail, bool $checkId = false): void
    {
        $query = self::where('user_id', $userDetail->user_id)
            ->where('key', $userDetail->key);

        if ($checkId && $userDetail->id) {
            $query->where('id', '<>', $userDetail->id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'key' => __('auth.user_detail_key_exists', [
                    'attribute' => $userDetail->key,
                ]),
            ]);
        }
    }
}
