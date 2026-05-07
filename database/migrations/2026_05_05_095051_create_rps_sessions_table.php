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
        Schema::create('rps_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rps_id');
            $table->integer('session_number'); // 1-14
            $table->string('topic_name');
            $table->text('sub_clo'); // Topic Learning Objective
            $table->text('materi_pembelajaran');
            // Assessment per Session
            $table->string('assessment_type')->nullable();
            $table->text('assessment_output')->nullable();
            $table->integer('weight')->default(0);
            
            $table->timestamps();

            $table->foreign('rps_id')->references('id')->on('rps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rps_sessions');
        Schema::enableForeignKeyConstraints();
    }
};
