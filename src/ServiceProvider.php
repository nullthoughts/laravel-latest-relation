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

        // Builder::macro('latestRelation', function () 
        // {
        //     if(! $where = $this->wheres[0]['second'] ?? null) {
        //         throw new \InvalidArgumentException('Second join parameter is not specified. The latestRelation() method should only be called from within a whereHas() on a hasMany relationship.');
        //      }

        //     list($table, $parentRelatedColumn) = explode('.', $where);

        //     return $this->where($table . '.id', function ($sub) use ($where, $table, $parentRelatedColumn) {
        //         $sub->select('id')
        //             ->from($table . ' AS sub')
        //             ->whereColumn('sub.' . $parentRelatedColumn, $where)
        //             ->latest()
        //             ->take(1);
        //     });
        // });

        // Builder::macro('whereLatest', function ($column, $value) 
        // {
        //     return $this->latestRelation()->where($column, $value);
        // });

        // Builder::macro('latestRelationTwo', function () {
        //     if(! $where = $this->wheres[0] ?? null) {
        //         throw new \InvalidArgumentException('Second join parameter is not specified. The latestRelation() method should only be called from within a whereHas() on a hasMany relationship.');
        //     }

        //     return $this->where('id', function ($sub) use ($where) {
        //             $sub->from($this->from)
        //             ->selectRaw('max(id)')
        //             ->whereRaw($where['first']  . ' = ' . $where['second']);
        //         });
        // });

        // Builder::macro('whereLatestTwo', function ($column, $value) {
        //     return $this->latestRelationTwo()->where($column, $value);
        // });
    }
}