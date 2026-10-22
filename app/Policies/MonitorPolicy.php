<?php

namespace App\Policies;

use App\Models\Monitor;
use App\Models\User;

class MonitorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->organizations()->exists();
    }

    public function view(User $user, Monitor $monitor): bool
    {
        return $user->organizations()->whereKey($monitor->organization_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->organizations()->exists();
    }

    public function update(User $user, Monitor $monitor): bool
    {
        return $this->view($user, $monitor);
    }

    public function delete(User $user, Monitor $monitor): bool
    {
        return $this->view($user, $monitor);
    }
}
