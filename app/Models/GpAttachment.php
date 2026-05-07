<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class GpAttachment extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $fillable = [
        'id_prodi',
        'nm_dokumen',
        'file_path',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
}
