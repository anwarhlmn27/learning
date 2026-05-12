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
        'subject_id',
        'kode_clo',
        'deskripsi',
        'bloom_level',
    ];

    /**
     * Get the subject this CLO belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the PLOs this CLO is mapped to.
     */
    public function plos()
    {
        return $this->belongsToMany(Plo::class, 'clo_plo', 'clo_id', 'plo_id')->using(CloPlo::class)->withTimestamps();
    }


}
