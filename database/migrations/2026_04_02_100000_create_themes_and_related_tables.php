
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // THEMES TABLE
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('preview_image_url')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('display_order');
        });

        // PIVOT TABLE
        Schema::create('main_category_theme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_category_id')
                ->constrained('main_categories')
                ->cascadeOnDelete();

            $table->foreignId('theme_id')
                ->constrained('themes')
                ->cascadeOnDelete();

            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['main_category_id', 'theme_id']);
        });

        // PURCHASE TABLE
        Schema::create('user_theme_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('theme_id')
                ->constrained('themes')
                ->cascadeOnDelete();

            $table->foreignId('main_category_id')
                ->constrained('main_categories')
                ->cascadeOnDelete();

            $table->timestamp('purchase_date');
            $table->decimal('amount_paid', 10, 2);
            $table->string('transaction_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'theme_id', 'main_category_id']);
        });

        // ADD COLUMN TO main_categories (SAFE CHECK)
        if (Schema::hasTable('main_categories') &&
            !Schema::hasColumn('main_categories', 'default_theme_id')) {

            Schema::table('main_categories', function (Blueprint $table) {
                $table->foreignId('default_theme_id')
                    ->nullable()
                    ->constrained('themes')
                    ->nullOnDelete()
                    ->after('id');
            });
        }
    }

    public function down(): void
    {
        // DROP FOREIGN COLUMN FIRST (SAFE)
        if (Schema::hasTable('main_categories') &&
            Schema::hasColumn('main_categories', 'default_theme_id')) {

            Schema::table('main_categories', function (Blueprint $table) {
                $table->dropConstrainedForeignId('default_theme_id');
            });
        }

        Schema::dropIfExists('user_theme_purchases');
        Schema::dropIfExists('main_category_theme');
        Schema::dropIfExists('themes');
    }
};