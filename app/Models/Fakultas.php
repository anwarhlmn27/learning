<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Fakultas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fakultas';

    protected $fillable = [
        'id_univs',
        'kode_fakultas',
        'nama_fakultas',
        'short_name',
        'nama_pimpinan',
        'sign',
        'dekan_id',
    ];

    public function univ()
    {
        return $this->belongsTo(Univ::class, 'id_univs');
    }

    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'id_fakultas');
    }

    public function gps()
    {
        return $this->hasMany(Gp::class, 'id_prodi');
    }

    public function gpAttachments()
    {
        return $this->hasMany(GpAttachment::class, 'id_prodi');
    }

    public function visi()
    {
        return $this->morphOne(Visi::class, 'visible');
    }

    public function dekan()
    {
        return $this->belongsTo(User::class, 'dekan_id');
    }
}
