<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VisiDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id_visi',
        'type',
        'urutan',
        'konten',
    ];

    /**
     * Get the visi that owns the detail.
     */
    public function visi()
    {
        return $this->belongsTo(Visi::class, 'id_visi');
    }
}
