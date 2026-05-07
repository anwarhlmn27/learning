<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Univ extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'univs';

    protected $fillable = [
        'kode_univ',
        'nama_univ',
        'nama_pimpinan',
        'sign',
        'address',
        'email',
        'website',
        'rektor_id',
    ];

    public function fakultas()
    {
        return $this->hasMany(Fakultas::class, 'id_univs');
    }

    public function visi()
    {
        return $this->morphOne(Visi::class, 'visible');
    }

    public function rektor()
    {
        return $this->belongsTo(User::class, 'rektor_id');
    }
}
