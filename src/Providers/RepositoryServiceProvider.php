<?php

namespace HMsoft\Cms\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\PostRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\PostRepository::class
        );

        // Bind the scoped Category Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\CategoryRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\CategoryRepository::class
        );

        // Bind the scoped Attribute Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\AttributeRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\AttributeRepository::class
        );

        // Bind the polymorphic Feature Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\FeatureRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\FeatureRepository::class
        );

        // Bind the polymorphic Download Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\DownloadRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\DownloadRepository::class
        );

        // Bind the polymorphic Faq Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\FaqRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\FaqRepository::class
        );

        // Bind the polymorphic Plan Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\PlanRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\PlanRepository::class
        );

        // Bind the polymorphic Media Repository
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\MediaRepository::class
        );


        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\BusinessSettingRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\BusinessSettingRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\SliderRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\SliderRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\SectorRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\SectorRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\UserRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\UserRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\PagesMetaRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\PagesMetaRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\TestimonialRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\TestimonialRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\TeamRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\TeamRepository::class
        );
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\StatisticsRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\StatisticsRepository::class
        );

        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\OrganizationRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\OrganizationRepository::class
        );

        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\LegalRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\LegalRepository::class
        );
        // contact us
        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\ContactUsRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\ContactUsRepository::class
        );


        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\PagesMetaRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\PagesMetaRepository::class
        );

        $this->app->bind(
            \HMsoft\Cms\Repositories\Contracts\LangRepositoryInterface::class,
            \HMsoft\Cms\Repositories\Eloquent\LangRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
