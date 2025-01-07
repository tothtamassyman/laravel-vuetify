<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
     * Get the details for the user.
     */
    public function details(): HasMany
    {
        return $this->hasMany(UserDetail::class);
    }

    /**
     * Get the detail value for the given key.
     *
     * @param  string  $key
     * @return string|null
     */
    public function getDetail(string $key): ?string
    {
        $detail = $this->details()->where('key', $key)->first();
        if ($detail && is_scalar($detail->value)) {
            return (string) $detail->value;
        }
        return null;
    }

    /**
     * Set the detail value for the given key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return UserDetail
     */
    public function setDetail(string $key, mixed $value): UserDetail
    {
        return $this->details()->updateOrCreate(
            ['key' => $key, 'user_id' => $this->id],
            ['value' => $value]
        );
    }
}
