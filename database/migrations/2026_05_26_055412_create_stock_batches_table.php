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
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('initial_quantity');
            $table->integer('remaining_quantity');
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->timestamp('arrived_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['product_id', 'warehouse_id', 'remaining_quantity']);
            $table->index('arrived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
