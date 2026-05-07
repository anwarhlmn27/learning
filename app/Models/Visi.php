<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Visi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'visible_id',
        'visible_type',
        'visi',
        'doc_penyusunan',
        'doc_pengesahan',
        'doc_sosialisasi',
        'doc_hasil_survey',
    ];

    /**
     * Get the parent visible model (Univ, Fakultas, or Prodi).
     */
    public function visible()
    {
        return $this->morphTo();
    }

    /**
     * Get the details (misi, tujuan, strategi) for the visi.
     */
    public function details()
    {
        return $this->hasMany(VisiDetail::class, 'id_visi')->orderBy('urutan');
    }
}
