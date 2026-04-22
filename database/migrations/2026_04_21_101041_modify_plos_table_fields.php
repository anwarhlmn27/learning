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
        Schema::table('plos', function (Blueprint $table) {
            $table->text('title_plo')->change();
            $table->text('plo')->change();
            $table->text('detail')->nullable()->change();
            $table->text('deskripsi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plos', function (Blueprint $table) {
            $table->string('title_plo')->change();
            $table->string('plo')->change();
            $table->string('detail')->change();
            $table->string('deskripsi')->change();
        });
    }
};
