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
        'misi',
        'tujuan1',
        'tujuan2',
        'tujuan3',
        'tujuan4',
        'tujuan5',
        'strategi1',
        'strategi2',
        'strategi3',
        'strategi4',
        'strategi5',
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
}
