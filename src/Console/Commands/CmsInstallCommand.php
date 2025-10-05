<?php

namespace HMsoft\Cms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use HMsoft\Cms\Providers\CmsServiceProvider;

class CmsInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all the CMS package resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CMS package installation...');

        // 1. نشر ملف الإعدادات
        $this->comment('Publishing configuration file...');
        Artisan::call('vendor:publish', [
            '--provider' => CmsServiceProvider::class,
            '--tag' => 'cms-config',
            '--force' => true,
        ]);

        // php artisan vendor:publish --provider="HMsoft\Cms\Providers\CmsServiceProvider" --tag="cms-config" --force
        
        $this->info('Configuration file published.');

        // 2. تشغيل الـ Migrations
        $this->comment('Running database migrations...');
        Artisan::call('migrate');
        $this->info('Migrations completed.');

        // 3. تشغيل الـ Seeder الخاص بالصلاحيات
        $this->comment('Seeding authorization data (roles and permissions)...');
        Artisan::call('db:seed', [
            '--class' => '\\Database\\Seeders\\AuthorizationSeeder'
        ]);
        $this->info('Authorization data seeded.');

        $this->info('✅ CMS package installed successfully!');

        return self::SUCCESS;
    }
}
