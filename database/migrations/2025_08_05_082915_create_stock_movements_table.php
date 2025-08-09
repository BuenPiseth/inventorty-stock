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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // Stock in or stock out
            $table->integer('quantity'); // Quantity moved
            $table->integer('previous_stock'); // Stock before movement
            $table->integer('new_stock'); // Stock after movement
            $table->decimal('unit_cost', 10, 2)->nullable(); // Cost per unit (for stock in)
            $table->string('reference')->nullable(); // Reference number (PO, invoice, etc.)
            $table->text('notes')->nullable(); // Additional notes
            $table->string('reason')->nullable(); // Reason for movement
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('movement_date'); // When the movement occurred
            $table->timestamps();

            // Indexes for better performance
            $table->index(['product_id', 'type']);
            $table->index('movement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
