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
        Schema::create('class_topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_room_id');
            $table->integer('session_number'); // 1 sampai 14 (di-generate otomatis dari RPS)
            $table->string('title'); // Judul aktivitas/topik
            $table->enum('type', ['materi', 'assignment', 'forum', 'quiz']);
            $table->uuid('content_id'); // Polymorphic ID (merujuk ke id materi/assignment/forum/quiz)
            $table->timestamps();

            $table->foreign('class_room_id')->references('id')->on('class_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_topics');
    }
};
