<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class Subject extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $table = 'subjects';

    protected $fillable = [
        'id_prodi',
        'kode_subject',
        'nama_subject',
        'sks_t',
        'sks_p',
        'total_sks',
        'jenis_subject',
        'deskripsi',
        'prerequisite_id',
        'semester',
        'status',
    ];

    /**
     * Get the prodi that owns the subject.
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    /**
     * Get the prerequisite subject.
     */
    public function prerequisite()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_id');
    }

    /**
     * Get the assessments for this subject.
     */
    public function assessments()
    {
        return $this->hasMany(SubjectAssessment::class, 'subject_id');
    }

    /**
     * Get the bahan kajian mapping for this subject.
     */
    public function bks()
    {
        return $this->belongsToMany(BahanKajian::class, 'subject_bk', 'subject_id', 'bk_id');
    }

    /**
     * Get the PLO mapping for this subject.
     */
    public function plos()
    {
        return $this->belongsToMany(Plo::class, 'subject_plo', 'subject_id', 'plo_id');
    }

    /**
     * Get the CLOs for this subject.
     */
    public function clos()
    {
        return $this->hasMany(Clo::class, 'subject_id');
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
