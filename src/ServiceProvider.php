<?php

namespace LaravelLatestRelation;

use InvalidArgumentException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        Builder::macro('latestRelation', function () {
            if(! $where = $this->wheres[0] ?? null) {
                throw new InvalidArgumentException('The latestRelation method should only be called from within a whereHas callback.');
            }

            return $this->where('id', function ($sub) use ($where) {
                $sub->from($this->from)
                ->selectRaw('max(id)')
                ->whereColumn($where['first'], $where['second']);
            });
        });

        Builder::macro('whereLatest', function ($column, $value) {
            return $this->latestRelation()->where($column, $value);
        });
    }
}