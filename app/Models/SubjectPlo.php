<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubjectPlo extends Pivot
{
    use HasUuids;

    protected $table = 'subject_plo';

    public $incrementing = false;
    protected $keyType = 'string';
}
