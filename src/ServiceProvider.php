<?php

namespace LaravelLatestRelation;

use InvalidArgumentException;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        QueryBuilder::macro('relation', function (string $type = 'max') {
            if(! $where = $this->wheres[0] ?? null) {
                throw new InvalidArgumentException('The relation methods should only be called from within a whereHas callback.');
            }

            return $this->where('id', function ($sub) use ($where, $type) {
                $sub->from($this->from)
                    ->selectRaw($type . '(id)')
                    ->whereColumn($where['first'], $where['second']);
            });
        });

        QueryBuilder::macro('earliestRelation', function () {
            return $this->relation('min');
        });

        QueryBuilder::macro('latestRelation', function () {
            return $this->relation('max');
        });

        QueryBuilder::macro('whereEarliest', function ($column, $operator = null, $value = null) {
            return $this->earliestRelation()->where($column, $operator, $value);
        });

        QueryBuilder::macro('whereLatest', function ($column, $operator = null, $value = null) {
            return $this->latestRelation()->where($column, $operator, $value);
        });

        EloquentBuilder::macro('whereEarliestRelation', function ($relation, $column, $operator = null, $value = null) {
            return $this->whereHas($relation, function($query) use ($column, $operator, $value) {
                return $query->whereEarliest($column, $operator, $value);
            });
        });

        EloquentBuilder::macro('whereLatestRelation', function ($relation, $column,  $operator = null, $value = null) {
            return $this->whereHas($relation, function ($query) use ($column, $operator, $value) {
                $query->whereLatest($column, $operator, $value);
            });
        });
    }
}
