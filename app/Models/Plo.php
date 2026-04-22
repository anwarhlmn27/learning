<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Plo extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_prodi',
        'id_gp',
        'title_plo',
        'plo',
        'detail',
        'deskripsi',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function gp()
    {
        return $this->belongsTo(Gp::class, 'id_gp');
    }
}
