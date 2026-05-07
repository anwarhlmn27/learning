<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CloPlo extends Pivot
{
    use HasUuids;

    protected $table = 'clo_plo';

    public $incrementing = false;
    protected $keyType = 'string';
}
