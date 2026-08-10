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
        // Untuk menampung Modul, PPT, Link Video, dll
        Schema::create('materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rps_resource_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Untuk PDF/PPT
            $table->string('original_filename')->nullable();
            $table->string('link_url')->nullable();  // Untuk link eksternal
            $table->timestamps();

            $table->foreign('rps_resource_id')->references('id')->on('session_resources')->onDelete('set null');
        });

        // Untuk Ruang Diskusi
        Schema::create('forums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('context');
            $table->timestamps();
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('forum_id');
            $table->uuid('user_id');
            $table->uuid('parent_id')->nullable();
            $table->text('content');
            $table->timestamps();

            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('forum_posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forums');
        Schema::dropIfExists('materials');
    }
};
