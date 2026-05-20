<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class BahanKajian extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $fillable = [
        'id_prodi',
        'id_kategori_bk',
        'kode_bk',
        'nm_bahan_kajian',
        'deskripsi',
        'sub_bk',
        'tingkat_kedalaman',
        'sumber_acuan',
        'status',
    ];

    public function kategoriBK()
    {
        return $this->belongsTo(KategoriBK::class, 'id_kategori_bk');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function plos()
    {
        return $this->belongsToMany(Plo::class, 'bk_plos', 'id_bk', 'id_plo')->using(BkPlo::class)->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_bk', 'bk_id', 'subject_id')->using(SubjectBk::class)->withTimestamps();
    }
}
