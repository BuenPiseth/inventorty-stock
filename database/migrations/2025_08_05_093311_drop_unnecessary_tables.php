<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop unnecessary tables for streamlined inventory system
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('units');
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't recreate these tables as they're not needed
        // If needed, run the original migrations
    }
};
