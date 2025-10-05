<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Team\Team;

use Illuminate\Auth\Access\Response;

class TeamPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any team members.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view team members
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('teams.view');
    }

    /**
     * Determine whether the user can view the team member.
     */
    public function view(CmsUserInterface $user, Team $team): bool
    {
        // Allow guest users to view team members
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('teams.view');
    }

    /**
     * Determine whether the user can create team members.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('teams.create');
    }

    /**
     * Determine whether the user can update the team member.
     */
    public function update(CmsUserInterface $user, Team $team): bool
    {
        return $this->authService->hasPermission('teams.edit');
    }

    /**
     * Determine whether the user can delete the team member.
     */
    public function delete(CmsUserInterface $user, Team $team): bool
    {
        return $this->authService->hasPermission('teams.delete');
    }

    /**
     * Determine whether the user can restore the team member.
     */
    public function restore(CmsUserInterface $user, Team $team): bool
    {
        return $this->authService->hasPermission('teams.delete');
    }

    /**
     * Determine whether the user can permanently delete the team member.
     */
    public function forceDelete(CmsUserInterface $user, Team $team): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
