<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopesByProdi
{
    protected static function bootScopesByProdi()
    {
        static::addGlobalScope('prodi', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('kaprodi')) {
                $model = $builder->getModel();
                $column = $model instanceof \App\Models\Prodi ? 'id' : 'id_prodi';
                $prodiIds = \App\Models\Prodi::where('kaprodi_id', auth()->id())->pluck('id');
                $builder->whereIn($model->getTable() . '.' . $column, $prodiIds);
            }
        });
    }
}
