<?php

namespace App\Policies;

use App\Models\User;

class ActivityLogPolicy
{
    /**
     * Only admins may view activity logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->usertype === 'admin';
    }
}
