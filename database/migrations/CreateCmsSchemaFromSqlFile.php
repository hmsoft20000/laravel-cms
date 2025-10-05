<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // حدد المسار إلى ملف الـ SQL داخل الحزمة
        $path = __DIR__ . '/../../database/schema/mysql-schema.sql';

        // اقرأ محتوى الملف وقم بتنفيذه كـ "استعلام خام"
        $sql = File::get($path);
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // للتراجع، ستحتاج إلى حذف كل الجداول التي تم إنشاؤها
        // يجب أن تضيف أسماء كل جداولك هنا
        // Schema::dropIfExists('posts');
        // Schema::dropIfExists('users');
        // Schema::dropIfExists('roles');
        // Schema::dropIfExists('permissions');
        // ... وهكذا
    }
};
