<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dosens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('prodi_id');
            $table->string('nidn')->unique();
            $table->string('nama_dosen');
            $table->string('gelar')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prodi_id')->references('id')->on('prodis')->onDelete('cascade');
        });

        $defaultProdi = Prodi::first();
        if ($defaultProdi) {
            $users = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['rektor', 'dekan', 'kaprodi']);
            })->whereDoesntHave('dosen')->get();

            foreach ($users as $user) {
                Dosen::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'prodi_id' => $defaultProdi->id,
                    'nidn' => 'TEMP-' . rand(10000000, 99999999),
                    'nama_dosen' => $user->name,
                    'gelar' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
