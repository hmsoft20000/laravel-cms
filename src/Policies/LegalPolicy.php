<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Legal\Legal;

use Illuminate\Auth\Access\Response;

class LegalPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any legal documents.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view legal documents
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('settings.view') || $this->authService->hasPermission('legal.view');
    }

    /**
     * Determine whether the user can view the legal document.
     */
    public function view(CmsUserInterface $user, Legal $legal): bool
    {
        // Public legal documents can be viewed by anyone (including guests)
        if ($legal->is_public ?? false) {
            return true;
        }

        // Allow guest users to view basic legal documents
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('settings.view') || $this->authService->hasPermission('legal.view');
    }

    /**
     * Determine whether the user can create legal documents.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('settings.edit') || $this->authService->hasPermission('legal.edit');
    }

    /**
     * Determine whether the user can update the legal document.
     */
    public function update(CmsUserInterface $user, Legal $legal): bool
    {
        return $this->authService->hasPermission('settings.edit') || $this->authService->hasPermission('legal.edit');
    }

    /**
     * Determine whether the user can delete the legal document.
     */
    public function delete(CmsUserInterface $user, Legal $legal): bool
    {
        // Only super admins can delete legal documents
        return $this->authService->hasRole('super-admin');
    }

    /**
     * Determine whether the user can publish/unpublish the legal document.
     */
    public function publish(CmsUserInterface $user, Legal $legal): bool
    {
        return $this->authService->hasPermission('settings.edit') || $this->authService->hasPermission('legal.edit');
    }

    /**
     * Determine whether the user can manage media for legal documents.
     */
    public function manageMedia(CmsUserInterface $user, Legal $legal): bool
    {
        return $this->authService->hasPermission('settings.edit') || $this->authService->hasPermission('legal.edit');
    }

    /**
     * Determine whether the user can manage specific types of legal documents.
     */
    public function manageType(CmsUserInterface $user, string $type): bool
    {
        // Define which user roles can manage specific legal document types
        $typePermissions = [
            'privacy-policy' => ['settings.edit', 'legal.edit'],
            'terms-of-service' => ['settings.edit', 'legal.edit'],
            'about-us' => ['settings.edit', 'legal.edit'],
            'contact-us' => ['settings.edit', 'legal.edit'],
        ];

        if (isset($typePermissions[$type])) {
            foreach ($typePermissions[$type] as $permission) {
                if ($this->authService->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Legal $legal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Legal $legal): bool
    {
        return false;
    }
}
