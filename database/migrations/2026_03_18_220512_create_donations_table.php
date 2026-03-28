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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')
              ->nullable()
              ->unique()  
              ->constrained()
              ->nullOnDelete();
            $table->decimal('amount_usd', 10, 2)->default(0);
            $table->decimal('amount_khr', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qr_code', 'other'])->default('cash');
            $table->enum('cash_method', ['usd', 'khr', 'both'])->default('usd');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
