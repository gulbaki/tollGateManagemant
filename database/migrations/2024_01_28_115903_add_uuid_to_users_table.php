<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the UUID column. You might want it to be unique.
            $table->uuid('uuid')->after('id')->unique();

            // Optionally, set the UUID field to be indexed for better performance
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the UUID column
            $table->dropIndex(['uuid']); // Drop index first
            $table->dropColumn('uuid');
        });
    }
};
