<?php

namespace Perevorotcom\Laraveloctober\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Localization;

class TranslatableScope implements Scope
{
    /**
     * Apply translatable scope to a given Eloquent query builder.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!$model->ignoreTranslated && !empty($model->getPrimatyTranslatableMutators())) {
            if (Localization::getDefaultLocale() != Localization::getCurrentLocale()) {
                if (empty($model->table)) {
                    abort(500, 'Для `'.get_class($model).'` не указана переменная $table');
                }

                $builder->select($model->table.'.*');
                $builder->join($model->translationIndexesTable, $model->table.'.id', '=', $model->translationIndexesTable.'.model_id');

                $builder->where(function ($q) use ($model) {
                    $q->where($model->translationIndexesTable.'.model_type', $model->backendModel);
                    $q->where($model->translationIndexesTable.'.locale', Localization::getCurrentLocale());

                    foreach ($model->getPrimatyTranslatableMutators() as $column) {
                        $q->where(function ($q) use ($column, $model) {
                            $q->where($model->translationIndexesTable.'.item', $column);
                            $q->where($model->translationIndexesTable.'.value', '!=', '');
                        });
                    }
                });
            } else {
                foreach ($model->getPrimatyTranslatableMutators() as $column) {
                    $builder->where($model->table.'.'.$column, '!=', '');
                    $builder->whereNotNull($model->table.'.'.$column);
                }
            }
        }
    }
}
