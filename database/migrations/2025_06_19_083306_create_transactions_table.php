<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('transactions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('company_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('cash_register_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->enum('type', ['regular', 'emresa']);
    $table->enum('metal_type', ['oro', 'plata'])->default('oro');

    $table->decimal('grams', 10, 3);
    $table->decimal('purity', 5, 4)->nullable();
    $table->decimal('discount_percentage', 5, 2)->default(0);

    $table->decimal('price_per_gram_pen', 15, 8)->nullable();
    $table->decimal('price_per_gram_usd', 15, 8)->nullable();
    $table->decimal('price_per_gram_bob', 15, 8)->nullable();
    $table->decimal('price_per_oz', 15, 4)->nullable();

    $table->decimal('total_pen', 15, 8)->nullable();
    $table->decimal('total_usd', 15, 8)->nullable();
    $table->decimal('total_bob', 15, 8)->nullable();

    $table->string('moneda', 5)->default('PEN');
    $table->decimal('exchange_rate_pen_usd', 10, 3)->nullable();

    $table->string('client_name')->nullable();
    $table->string('tipo_venta')->nullable();
    $table->time('hora')->nullable();

    $table->foreignId('created_by')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
