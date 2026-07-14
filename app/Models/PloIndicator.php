<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PloIndicator extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'plo_id',
        'indicator_code',
        'indicator_description',
    ];

    public function plo()
    {
        return $this->belongsTo(Plo::class, 'plo_id');
    }
}
