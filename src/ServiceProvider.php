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

        QueryBuilder::macro('whereEarliest', function ($column, $value) {
            return $this->earliestRelation()->where($column, $value);
        });

        QueryBuilder::macro('whereLatest', function ($column, $value) {
            return $this->latestRelation()->where($column, $value);
        });

        EloquentBuilder::macro('whereEarliestRelation', function ($relation, $column, $value) {
            return $this->whereHas($relation, function($query) use ($column, $value) {
                return $query->whereEarliest($column, $value);
            });
        });

        EloquentBuilder::macro('whereLatestRelation', function ($relation, $column, $value) {
            return $this->whereHas($relation, function ($query) use ($column, $value) {
                $query->whereLatest($column, $value);
            });
        });
    }
}