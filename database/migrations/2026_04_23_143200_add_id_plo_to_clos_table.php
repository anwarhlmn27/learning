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
        Schema::table('clos', function (Blueprint $table) {
            $table->uuid('id_plo')->nullable()->after('id_subject');
            $table->foreign('id_plo')->references('id')->on('plos')->onDelete('set null');
            $table->foreign('id_subject')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clos', function (Blueprint $table) {
            $table->dropForeign(['id_plo']);
            $table->dropForeign(['id_subject']);
            $table->dropColumn('id_plo');
        });
    }
};
