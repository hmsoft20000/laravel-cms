<?php

namespace HMsoft\Cms\Traits\General;

use HMsoft\Cms\Models\Organizations\Organization;

trait Linkable
{
    /** The base relationship */
    public function organizations()
    {
        return $this->morphToMany(Organization::class, 'linkable', 'organization_links')
            ->withPivot('role');
    }

    /** A filtered relationship for partners */
    public function partners()
    {
        // Assuming role  = Partner
        return $this->morphToMany(Organization::class, 'linkable', 'organization_links')
            ->wherePivot('role', 'partner');
    }

    /** A filtered relationship for sponsors */
    public function sponsors()
    {
        // Assuming role  = Sponsor
        return $this->morphToMany(Organization::class, 'linkable', 'organization_links')
            ->wherePivot('role', 'sponsor');
    }
}
