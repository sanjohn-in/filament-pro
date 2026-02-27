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
        Schema::create('cars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('model_id')->constrained('car_models')->cascadeOnDelete();
        $table->foreignId('owner_id')->nullable()->constrained()->cascadeOnDelete();
        $table->decimal('price', 10, 2);
        $table->decimal('interest', 10, 2);
        $table->enum('contract', [3, 6, 12, 18, 24])->default(3);
        $table->string('start_date')->nullable();
        $table->string('end_date')->nullable();
        $table->year('year')->nullable();
        $table->boolean('is_active')->default(true);
        $table->text('note')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lists');
    }
};
