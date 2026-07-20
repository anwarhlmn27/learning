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
        'sks_pl',
        'total_sks',
        'jenis_subject',
        'deskripsi',
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
     * Get the prerequisite subjects.
     */
    public function prerequisites()
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisite', 'subject_id', 'prerequisite_id')
                    ->using(SubjectPrerequisite::class)
                    ->withTimestamps();
    }



    /**
     * Get the bahan kajian mapping for this subject.
     */
    public function bks()
    {
        return $this->belongsToMany(BahanKajian::class, 'subject_bk', 'subject_id', 'bk_id')
                    ->using(SubjectBk::class)
                    ->withTimestamps();
    }

    /**
     * Get the PLO mapping for this subject.
     */
    public function plos()
    {
        return $this->belongsToMany(Plo::class, 'subject_plo', 'subject_id', 'plo_id')
                    ->using(SubjectPlo::class)
                    ->withPivot('mapping_level')
                    ->withTimestamps();
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
        return $this->belongsToMany(Subject::class, 'subject_prerequisite', 'prerequisite_id', 'subject_id')
                    ->using(SubjectPrerequisite::class)
                    ->withTimestamps();
    }

    /**
     * Get the RPS for this subject.
     */
    public function rps()
    {
        return $this->hasMany(Rps::class, 'subject_id');
    }
}
