<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Gp extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_prodi',
        'nm_profil',
        'deskripsi',
        'expertise',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
}
