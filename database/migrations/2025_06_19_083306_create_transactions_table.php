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

            // 🔹 Relación con caja
            $table->foreignId('cash_register_id')->constrained()->onDelete('cascade');

            // 🔹 Tipo de operación
            $table->enum('type', ['compra', 'venta']);
            $table->enum('metal_type', ['oro', 'plata'])->default('oro');

            // 🔹 Datos principales
            $table->decimal('grams', 10, 3);
            $table->decimal('purity', 5, 4)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);

            // 🔹 Precios por gramo / onza en distintas monedas
            $table->decimal('price_per_gram_pen', 15, 8)->nullable();
            $table->decimal('price_per_gram_bob', 15, 8)->nullable();
            $table->decimal('price_per_gram_usd', 15, 8)->nullable();
            $table->decimal('price_per_oz', 15, 4)->nullable();

            // 🔹 Totales en monedas
            $table->decimal('total_pen', 15, 8)->nullable();
            $table->decimal('total_bob', 15, 8)->nullable();
            $table->decimal('total_usd', 15, 8)->nullable();

            // 🔹 Tipo de cambio y moneda base
            $table->string('moneda', 5)->default('PEN'); // moneda de la transacción
            $table->decimal('exchange_rate_pen_bob', 10, 3)->nullable(); // PEN ↔ BOB
            $table->decimal('exchange_rate_pen_usd', 10, 3)->nullable(); // PEN ↔ USD

            // 🔹 Cliente y detalles extra
            $table->string('client_name')->nullable();
            $table->string('tipo_venta')->nullable(); // regular / empresa u otros
            $table->time('hora')->nullable();

            // 🔹 Auditoría
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
