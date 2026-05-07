<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubjectAssessment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'subject_assessments';

    protected $fillable = [
        'subject_id',
        'name',
        'weight',
        'rubric_link',
    ];

    /**
     * Get the subject that owns the assessment.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the CLOs this assessment maps to.
     */
    public function clos()
    {
        return $this->belongsToMany(Clo::class, 'assessment_clo', 'assessment_id', 'clo_id');
    }
}
