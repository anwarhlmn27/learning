<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class Gp extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $fillable = [
        'id_prodi',
        'kode_profil',
        'nm_profil',
        'deskripsi',
        'career_pathway',
        'kompetensi',
        'sumber_acuan',
        'stakeholders',
        'status',
        'file',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function plos()
    {
        return $this->belongsToMany(Plo::class, 'plo_gps', 'id_gp', 'id_plo')->using(PloGp::class)->withTimestamps();
    }
}
