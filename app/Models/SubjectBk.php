<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubjectBk extends Pivot
{
    use HasUuids;

    protected $table = 'subject_bk';

    public $incrementing = false;
    protected $keyType = 'string';
}
