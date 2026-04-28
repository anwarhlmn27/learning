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
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->string('name')->after('id')->nullable();
            $table->string('status')->default('active')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->integer('role')->nullable();
            $table->dropColumn('name');
            $table->dropColumn('status');
        });
    }
};
