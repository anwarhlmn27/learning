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
            $table->string('topic_name'); // Topic Pembelajaran
            $table->text('sub_clo'); // Topic Learning Objective sub-CMPK
            $table->text('assessment_indicators'); //Indikator Penilaian
            $table->text('evaluation_criteria'); //Kriteria Evaluasi
            $table->text('learning_materials'); //pengganti materi pembelajaran
            //Learning activity ada di tabel Rps_activity            
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
