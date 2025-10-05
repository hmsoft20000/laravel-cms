<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;



use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Shared\Attribute;


class AttributePolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any attributes.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('attributes.view');
    }

    /**
     * Determine whether the user can view the attribute.
     */
    public function view(CmsUserInterface $user, Attribute $attribute): bool
    {
        return $this->authService->hasPermission('attributes.view');
    }

    /**
     * Determine whether the user can create attributes.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('attributes.create');
    }

    /**
     * Determine whether the user can update the attribute.
     */
    public function update(CmsUserInterface $user, Attribute $attribute): bool
    {
        if (!$this->authService->hasPermission('attributes.edit')) {
            return false;
        }

        // Authors can only edit their own attributes (unless they have publish permission)
        if (!$this->authService->hasPermission('attributes.publish')) {
            return $attribute->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the attribute.
     */
    public function delete(CmsUserInterface $user, Attribute $attribute): bool
    {
        if (!$this->authService->hasPermission('attributes.delete')) {
            return false;
        }

        // Authors can only delete their own attributes (unless they have publish permission)
        if (!$this->authService->hasPermission('attributes.publish')) {
            return $attribute->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the attribute.
     */
    public function publish(CmsUserInterface $user, Attribute $attribute): bool
    {
        return $this->authService->hasPermission('attributes.publish');
    }

    /**
     * Determine whether the user can manage options for the attribute.
     */
    public function manageOptions(CmsUserInterface $user, Attribute $attribute): bool
    {
        if (!$this->authService->hasPermission('attributes.edit')) {
            return false;
        }

        // Authors can only manage options for their own attributes (unless they have publish permission)
        if (!$this->authService->hasPermission('attributes.publish')) {
            return $attribute->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the attribute.
     */
    public function restore(CmsUserInterface $user, Attribute $attribute): bool
    {
        return $this->authService->hasPermission('attributes.delete');
    }

    /**
     * Determine whether the user can permanently delete the attribute.
     */
    public function forceDelete(CmsUserInterface $user, Attribute $attribute): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
