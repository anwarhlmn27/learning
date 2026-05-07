<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class Plo extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $fillable = [
        'id_prodi',
        'kode_plo',
        'plo_title',
        'rumusan_plo',
        'domain',
        'bloom_level',
        'kko',
        'indikator_ketercapaian',
        'target_capaian',
        'metode_pengukuran',
        'status',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function gps()
    {
        return $this->belongsToMany(Gp::class, 'plo_gps', 'id_plo', 'id_gp')->using(PloGp::class)->withTimestamps();
    }

    public function clos()
    {
        return $this->belongsToMany(Clo::class, 'clo_plo', 'plo_id', 'clo_id')->withTimestamps();
    }

    public function courseMapings()
    {
        return $this->hasMany(CourseMaping::class, 'id_plo');
    }
}
