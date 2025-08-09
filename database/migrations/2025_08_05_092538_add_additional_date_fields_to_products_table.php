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
            $table->date('expiry_date')->nullable()->after('status');
            $table->date('purchase_date')->nullable()->after('expiry_date');
            $table->date('last_stock_check')->nullable()->after('purchase_date');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('last_stock_check');
            $table->decimal('selling_price', 10, 2)->nullable()->after('purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'expiry_date',
                'purchase_date',
                'last_stock_check',
                'purchase_price',
                'selling_price'
            ]);
        });
    }
};
