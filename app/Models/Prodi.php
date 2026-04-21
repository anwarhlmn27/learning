<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Prodi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'prodis';

    protected $fillable = [
        'id_fakultas',
        'kode_prodi',
        'nama_prodi',
        'short_name',
        'nama_pimpinan',
        'sign',
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    public function visi()
    {
        return $this->morphOne(Visi::class, 'visible');
    }
}
