<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassRoom extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
