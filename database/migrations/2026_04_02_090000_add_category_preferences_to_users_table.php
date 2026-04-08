<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'category_preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('category_preferences')
                    ->nullable()
                    ->after('email')
                    ->comment('Stores user preferences per main category as JSON');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'category_preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('category_preferences');
            });
        }
    }
};