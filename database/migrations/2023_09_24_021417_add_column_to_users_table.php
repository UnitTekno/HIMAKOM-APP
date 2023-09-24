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
        Schema::table('users', function (Blueprint $table) {            
            $table->string('nim')->nullable()->unique();
            $table->string('nia')->nullable()->unique();
            $table->string('name_bagus')->nullable();
            $table->string('picture')->nullable();
            $table->year('year')->nullable();
            $table->string('device_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'nia', 'name_bagus', 'picture', 'year']);
        });
    }
};
