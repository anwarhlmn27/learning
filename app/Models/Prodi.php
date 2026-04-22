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
}
