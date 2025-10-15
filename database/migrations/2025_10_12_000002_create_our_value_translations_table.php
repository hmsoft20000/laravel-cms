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
        Schema::create('our_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('our_value_id')->constrained('our_values')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title');
            $table->text('description')->nullable();

            $table->unique(['our_value_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_value_translations');
    }
};
