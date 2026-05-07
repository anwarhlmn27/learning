<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class CourseMaping extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $table = 'course_mapings';

    protected $fillable = [
        'id_prodi',
        'id_subject',
        'level_maping',
        'id_plo',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'id_subject');
    }

    public function plo()
    {
        return $this->belongsTo(Plo::class, 'id_plo');
    }
}
