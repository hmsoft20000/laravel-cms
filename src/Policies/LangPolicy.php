<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Models\Lang;

class LangPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {  
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any langs.
     */
    public function viewAny($user): bool
    {
     
        // Allow guest users to view langs
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }
        
        return $this->authService->hasPermission('langs.view');
    }
    
    /**
     * Determine whether the user can view the lang.
     */
    public function view($user, Lang $lang): bool
    {
        info("sssss");
        // Allow guest users to view langs
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('langs.view');
    }

    /**
     * Determine whether the user can create langs.
     */
    public function create($user): bool
    {
        return $this->authService->hasPermission('langs.create');
    }

    /**
     * Determine whether the user can update the lang.
     */
    public function update($user, Lang $lang): bool
    {
        if (!$this->authService->hasPermission('langs.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('langs.publish')) {
            return $lang->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the lang.
     */
    public function delete($user, Lang $lang): bool
    {
        if (!$this->authService->hasPermission('langs.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('langs.publish')) {
            return $lang->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the lang.
     */
    public function publish($user, Lang $lang): bool
    {
        return $this->authService->hasPermission('langs.publish');
    }

    /**
     * Determine whether the user can manage images for the lang.
     */
    public function manageImages($user, Lang $lang): bool
    {
        if (!$this->authService->hasPermission('langs.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('langs.publish')) {
            return $lang->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Lang $lang): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Lang $lang): bool
    {
        return false;
    }
}
