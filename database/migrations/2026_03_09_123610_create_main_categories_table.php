<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('main_categories', function (Blueprint $table) {
            $table->id();
            // user_id nullable false, foreign key to users table
            $table->foreignId('user_id')
                ->constrained()       // references 'id' on 'users' table
                ->cascadeOnDelete();  // delete main_categories if user deleted

            $table->enum('type', ['wedding', 'engagement', 'handtied_ceremony', 'birthday', 'other'])
                  ->default('other');
            $table->integer('music_id')->nullable();
            $table->string('bride_name')->nullable();
            $table->string('groom_name')->nullable();
            $table->string('slug')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('address')->nullable();
            $table->text('google_map')->nullable();
            $table->text('cover_image')->nullable();
            $table->text('qr_code')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_categories');
    }
};
