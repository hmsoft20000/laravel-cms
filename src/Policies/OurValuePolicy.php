<?php

namespace HMsoft\Cms\Policies;

use App\Models\User;
use HMsoft\Cms\Models\OurValue\OurValue;

class OurValuePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('our_values.view');
    }

    public function view(User $user, OurValue $ourValue): bool
    {
        return $user->can('our_values.view');
    }

    public function create(User $user): bool
    {
        return $user->can('our_values.create');
    }

    public function update(User $user, OurValue $ourValue): bool
    {
        return $user->can('our_values.edit');
    }

    public function delete(User $user, OurValue $ourValue): bool
    {
        return $user->can('our_values.delete');
    }
}
