<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subject_prerequisite', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id');
            $table->uuid('prerequisite_id');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('prerequisite_id')->references('id')->on('subjects')->onDelete('cascade');
        });

        // Migrate existing prerequisites
        $subjects = DB::table('subjects')->whereNotNull('prerequisite_id')->get();
        foreach ($subjects as $subject) {
            DB::table('subject_prerequisite')->insert([
                'id' => (string) Str::uuid(),
                'subject_id' => $subject->id,
                'prerequisite_id' => $subject->prerequisite_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop the foreign key and the prerequisite_id column
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_id']);
            $table->dropColumn('prerequisite_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->uuid('prerequisite_id')->nullable();
            $table->foreign('prerequisite_id')->references('id')->on('subjects')->onDelete('set null');
        });

        // Copy back one prerequisite if any
        $pivots = DB::table('subject_prerequisite')->get();
        foreach ($pivots as $pivot) {
            DB::table('subjects')->where('id', $pivot->subject_id)->update([
                'prerequisite_id' => $pivot->prerequisite_id
            ]);
        }

        Schema::dropIfExists('subject_prerequisite');
    }
};
