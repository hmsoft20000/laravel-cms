<?php

namespace HMsoft\Cms\Policies;

use HMsoft\Cms\Contracts\AuthServiceInterface;



use HMsoft\Cms\Contracts\CmsUserInterface;
use HMsoft\Cms\Models\Testimonial\Testimonial;


class TestimonialPolicy
{

    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Determine whether the user can view any testimonials.
     */
    public function viewAny(CmsUserInterface $user): bool
    {
        // Allow guest users to view testimonials
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('testimonials.view');
    }

    /**
     * Determine whether the user can view the testimonial.
     */
    public function view(CmsUserInterface $user, Testimonial $testimonial): bool
    {
        // Allow guest users to view testimonials
        if (!$user || $this->authService->isAuthenticated() === false) {
            return true;
        }

        return $this->authService->hasPermission('testimonials.view');
    }

    /**
     * Determine whether the user can create testimonials.
     */
    public function create(CmsUserInterface $user): bool
    {
        return $this->authService->hasPermission('testimonials.create');
    }

    /**
     * Determine whether the user can update the testimonial.
     */
    public function update(CmsUserInterface $user, Testimonial $testimonial): bool
    {
        return $this->authService->hasPermission('testimonials.edit');
    }

    /**
     * Determine whether the user can delete the testimonial.
     */
    public function delete(CmsUserInterface $user, Testimonial $testimonial): bool
    {
        return $this->authService->hasPermission('testimonials.delete');
    }

    /**
     * Determine whether the user can restore the testimonial.
     */
    public function restore(CmsUserInterface $user, Testimonial $testimonial): bool
    {
        return $this->authService->hasPermission('testimonials.delete');
    }

    /**
     * Determine whether the user can permanently delete the testimonial.
     */
    public function forceDelete(CmsUserInterface $user, Testimonial $testimonial): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }
}
