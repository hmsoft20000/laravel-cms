<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('owner');
            $table->foreignId('parent_id')->nullable()->constrained('blogs')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_header')->default(false);
            $table->boolean('show_in_footer')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('short_content')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->unique(['blog_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_translations');
        Schema::dropIfExists('blogs');
    }
};
