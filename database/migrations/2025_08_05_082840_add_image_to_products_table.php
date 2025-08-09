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
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
            $table->text('description')->nullable()->after('image');
            $table->decimal('price', 10, 2)->nullable()->after('description');
            $table->string('sku')->unique()->nullable()->after('price');
            $table->integer('min_stock')->default(10)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image', 'description', 'price', 'sku', 'min_stock']);
        });
    }
};
