<?php

namespace HMsoft\Cms\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FixNamespaces extends Command
{
    protected $signature = 'fix:namespaces';
    protected $description = 'Fix namespaces and use statements in app directory based on PSR-4';

    public function handle()
    {
        $appPath = app_path();
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));

        foreach ($files as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;

            $fullPath = $file->getRealPath();
            $content = file_get_contents($fullPath);

            // احسب الـ namespace الصحيح بناءً على المسار
            $relativePath = str_replace($appPath . DIRECTORY_SEPARATOR, '', $fullPath);
            $namespacePath = str_replace('/', '\\', dirname($relativePath));
            $namespacePath = $namespacePath === '.' ? '' : '\\' . $namespacePath;
            $correctNamespace = 'App' . $namespacePath;

            // حدث namespace داخل الملف
            $content = preg_replace('/^namespace\s+[^;]+;/m', "namespace {$correctNamespace};", $content);

            file_put_contents($fullPath, $content);
            $this->info("Fixed: {$relativePath}");
        }

        $this->info('✅ All namespaces fixed successfully!');
    }
}
