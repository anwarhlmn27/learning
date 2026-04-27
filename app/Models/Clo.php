<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Clo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'clos';

    protected $fillable = [
        'id_subject',
        'id_plo',
        'clo',
        'deskripsi',
    ];

    /**
     * Get the subject this CLO belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'id_subject');
    }

    /**
     * Get the PLO this CLO is mapped to.
     */
    public function plo()
    {
        return $this->belongsTo(Plo::class, 'id_plo');
    }
}
