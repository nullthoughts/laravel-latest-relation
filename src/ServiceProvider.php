<?php

namespace LaravelLatestRelation;

use InvalidArgumentException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        Builder::macro('relation', function (string $type = 'max') {
            if(! $where = $this->wheres[0] ?? null) {
                throw new InvalidArgumentException('The relation methods should only be called from within a whereHas callback.');
            }

            return $this->where('id', function ($sub) use ($where, $type) {
                $sub->from($this->from)
                    ->selectRaw($type . '(id)')
                    ->whereColumn($where['first'], $where['second']);
            });
        });
        
        Builder::macro('earliestRelation', function () {
            return $this->relation('min');
        });

        Builder::macro('latestRelation', function () {
            return $this->relation('max');
        });

        Builder::macro('whereEarliest', function ($column, $value) {
            return $this->earliestRelation()->where($column, $value);
        });

        Builder::macro('whereLatest', function ($column, $value) {
            return $this->latestRelation()->where($column, $value);
        });
    }
}