<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class Prodi extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $table = 'prodis';

    protected $fillable = [
        'id_fakultas',
        'kode_prodi',
        'nama_prodi',
        'short_name',
        'nama_pimpinan',
        'sign',
        'kaprodi_id',
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    public function gps()
    {
        return $this->hasMany(Gp::class, 'id_prodi');
    }

    public function gpAttachments()
    {
        return $this->hasMany(GpAttachment::class, 'id_prodi');
    }

    public function plos()
    {
        return $this->hasMany(Plo::class, 'id_prodi');
    }

    public function visi()
    {
        return $this->morphOne(Visi::class, 'visible');
    }

    public function courseMapings()
    {
        return $this->hasMany(CourseMaping::class, 'id_prodi');
    }

    public function bahanKajians()
    {
        return $this->hasMany(BahanKajian::class, 'id_prodi');
    }

    public function kategoriBks()
    {
        return $this->hasMany(KategoriBK::class, 'id_prodi');
    }

    public function kaprodi()
    {
        return $this->belongsTo(User::class, 'kaprodi_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'id_prodi');
    }

    public function rps()
    {
        return $this->hasManyThrough(Rps::class, Subject::class, 'id_prodi', 'subject_id');
    }
}
