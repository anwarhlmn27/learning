<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Subject extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'subjects';

    protected $fillable = [
        'kode_subject',
        'nama_subject',
        'sks_t',
        'sks_p',
        'total_sks',
        'prerequisite_id',
        'semester',
        'assesment_type',
    ];

    protected $casts = [
        'assesment_type' => 'array',
    ];

    /**
     * Get the prerequisite subject.
     */
    public function prerequisite()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_id');
    }

    /**
     * Get the CLOs for this subject.
     */
    public function clos()
    {
        return $this->hasMany(Clo::class, 'id_subject');
    }

    /**
     * Get subjects that have this subject as a prerequisite.
     */
    public function dependents()
    {
        return $this->hasMany(Subject::class, 'prerequisite_id');
    }

    public function courseMapings()
    {
        return $this->hasMany(CourseMaping::class, 'id_subject');
    }
}
