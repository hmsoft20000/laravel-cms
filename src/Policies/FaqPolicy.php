<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;





use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Shared\Faq;

use Illuminate\Auth\Access\Response;

class FaqPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any faqs.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view FAQs
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('faqs.view');
    }

    /**
     * Determine whether the user can view the faq.
     */
    public function view(CmsUserInterface $user, Faq $faq): bool
    {
        // Allow guest users to view FAQs
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('faqs.view');
    }

    /**
     * Determine whether the user can create faqs.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('faqs.create');
    }

    /**
     * Determine whether the user can update the faq.
     */
    public function update(CmsUserInterface $user, Faq $faq): bool
    {
        if (!$this->authService->hasPermission('faqs.edit')) {
            return false;
        }

        if (!$this->authService->hasPermission('faqs.publish')) {
            return $faq->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the faq.
     */
    public function delete(CmsUserInterface $user, Faq $faq): bool
    {
        if (!$this->authService->hasPermission('faqs.delete')) {
            return false;
        }

        if (!$this->authService->hasPermission('faqs.publish')) {
            return $faq->user_id === $this->authService->getUserId();
        }

        return true;
    }

    /**
     * Determine whether the user can publish/unpublish the faq.
     */
    public function publish(CmsUserInterface $user, Faq $faq): bool
    {
        return $this->authService->hasPermission('faqs.publish');
    }

    /**
     * Determine whether the user can reorder faqs.
     */
    public function reorder(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('faqs.edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CmsUserInterface $user, Faq $faq): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CmsUserInterface $user, Faq $faq): bool
    {
        return false;
    }
}
