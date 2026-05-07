<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class KategoriBK extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $table = 'kategori_bk';

    protected $fillable = [
        'id_prodi',
        'nm_kategori',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function bahanKajians()
    {
        return $this->hasMany(BahanKajian::class, 'id_kategori_bk');
    }
}
