<?php

namespace App\Observers;

use App\Models\User;
use Exception;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     * @throws Exception
     */
    public function creating(User $user): void
    {
        if ($user->default_group_id === null) {
            $message = __('messages.user.default_group_id_required_on_create');
            throw new Exception($message);
        }
    }

    /**
     * Handle the User "updating" event.
     * @throws Exception
     */
    public function updating(User $user): void
    {
        if ($user->default_group_id === null) {
            $message = __('messages.user.default_group_id_required_on_update');
            throw new Exception($message);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
