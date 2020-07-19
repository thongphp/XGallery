<?php

namespace App\Traits\Jav;

use Illuminate\Database\Eloquent\Builder;

trait HasFilterValues
{
    /**
     * @param Builder $builder
     * @param string $column
     * @param array|string $value
     * @param string $operator
     */
    protected function processFilterValues(Builder $builder, string $column, $value, string $operator = '='): void
    {
        if (empty($value)) {
            return;
        }

        if (is_array($value)) {
            $builder->orWhereIn($column, $value);

            return;
        }

        $builder->orWhere($column, $operator, $value);
    }
}
