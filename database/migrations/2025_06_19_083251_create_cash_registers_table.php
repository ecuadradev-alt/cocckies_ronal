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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();

            // Apertura
            $table->decimal('opening_cash_pen', 12, 2)->default(0);
            $table->decimal('opening_cash_bob', 12, 2)->default(0);
            $table->decimal('opening_cash_usd', 12, 2)->default(0);
            $table->decimal('opening_gold', 12, 3)->default(0);

            // Saldos actuales
            $table->decimal('balance_pen', 12, 2)->default(0);
            $table->decimal('balance_bob', 12, 2)->default(0);
            $table->decimal('balance_usd', 12, 2)->default(0);

            // Cierre
            $table->decimal('closing_cash_pen', 12, 2)->nullable();
            $table->decimal('closing_cash_bob', 12, 2)->nullable();
            $table->decimal('closing_cash_usd', 12, 2)->nullable();
            $table->decimal('closing_gold', 12, 3)->nullable();

            // Estado de la caja
            $table->enum('status', ['abierta', 'cerrada'])->default('abierta');
            $table->text('notes')->nullable();

            // Auditoría
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
