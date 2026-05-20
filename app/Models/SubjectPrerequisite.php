<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubjectPrerequisite extends Pivot
{
    use HasUuids;

    protected $table = 'subject_prerequisite';

    public $incrementing = false;
    protected $keyType = 'string';
}
