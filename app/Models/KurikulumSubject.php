<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KurikulumSubject extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kurikulum_subjects';

    protected $fillable = [
        'id_kurikulum',
        'id_subject',
        'semester',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'id_subject');
    }
}
