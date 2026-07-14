<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PloTerm extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'plo_id',
        'description',
    ];

    public function plo()
    {
        return $this->belongsTo(Plo::class, 'plo_id');
    }
}
