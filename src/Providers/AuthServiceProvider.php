<?php

namespace HMsoft\Cms\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{


    /**
     * The model to policy mappings for the application.
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // DISABLED: All authorization logic commented out
        // Statistics
        // \HMsoft\Cms\Models\Statistics\Statistics::class => \HMsoft\Cms\Policies\StatisticsPolicy::class,

        // Testimonials
        // \HMsoft\Cms\Models\Testimonial\Testimonial::class => \HMsoft\Cms\Policies\TestimonialPolicy::class,

        // Team
        // \HMsoft\Cms\Models\Team\Team::class => \HMsoft\Cms\Policies\TeamPolicy::class,

        // Shared Models
        // \HMsoft\Cms\Models\Shared\Feature::class => \HMsoft\Cms\Policies\FeaturePolicy::class,
        // \HMsoft\Cms\Models\Shared\Download::class => \HMsoft\Cms\Policies\DownloadPolicy::class,
        // \HMsoft\Cms\Models\Shared\Faq::class => \HMsoft\Cms\Policies\FaqPolicy::class,
        // \HMsoft\Cms\Models\Shared\Category::class => \HMsoft\Cms\Policies\CategoryPolicy::class,
        // \HMsoft\Cms\Models\Shared\Attribute::class => \HMsoft\Cms\Policies\AttributePolicy::class,
        // \HMsoft\Cms\Models\Shared\Plan::class => \HMsoft\Cms\Policies\PlanPolicy::class,

        // Content Models
        // \HMsoft\Cms\Models\Content\Post::class => \HMsoft\Cms\Policies\PostPolicy::class,

        // Legal Models
        // \HMsoft\Cms\Models\Legal\Legal::class => \HMsoft\Cms\Policies\LegalPolicy::class,

        // Organization Models
        // \HMsoft\Cms\Models\Organizations\Organization::class => \HMsoft\Cms\Policies\OrganizationPolicy::class,

        // Sector Models
        // \HMsoft\Cms\Models\Sector\Sector::class => \HMsoft\Cms\Policies\SectorPolicy::class,

        // Page Meta Models
        // \HMsoft\Cms\Models\PageMeta\PageMeta::class => \HMsoft\Cms\Policies\PageMetaPolicy::class,

        // Business Settings
        // \HMsoft\Cms\Models\BusinessSetting::class => \HMsoft\Cms\Policies\BusinessSettingPolicy::class,

        // Authorization Models
        // \HMsoft\Cms\Models\Role::class => \HMsoft\Cms\Policies\RolePolicy::class,
        // \HMsoft\Cms\Models\Permission::class => \HMsoft\Cms\Policies\PermissionPolicy::class,

        // \HMsoft\Cms\Models\Lang::class => \HMsoft\Cms\Policies\LangPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {}
}
