<?php

namespace App\Traits\DataTables;

use Illuminate\Database\Eloquent\Builder;

trait CustomAdvancedFilter
{
    public function scopeFilterAndOrder(Builder $query)
    {
        $filters = request('f', []);
        $orderField = request('sort', 'id');
        $orderDirection = request('order', 'desc');
        $order = $orderField ? [$orderField => $orderDirection] : [];

        foreach ($filters as $fk => $filter2) {
            $filter = (array)json_decode($filter2);
            // Handle filter for a related model
            if (str_contains($filter['column'], '.')) {
                $relations = explode('.', $filter['column']);
                $filterColumn = array_pop($relations);

                $query->whereHas(implode(".", $relations), function (Builder $query) use ($filterColumn, $filter) {
                    $this->applyFilter($query, $filterColumn, $filter);
                });
            } else {
                // Handle filter for the current model
                $this->applyFilter($query, $filter['column'], $filter);
            }
        }

        foreach ($order as $field => $direction) {
            if (str_contains($field, '.')) {
                $relations = explode('.', $field);
                $orderField = array_pop($relations);
                $this->applyOrderBy($query, $relations, $orderField, $direction);
            } else {
                $query->orderBy($field, $direction);
            }
        }


        return $query->paginate(request('limit', config('project.pageLength')));
    }

    protected function applyFilter(Builder $query, string $column, array $filter)
    {
        if ($filter['operator'] === 'in') {
            $query->whereIn($column, $filter['query_1']);
        } elseif ($filter['operator'] === 'between') {
            $query->whereBetween($column, [$filter['query_1'][0], $filter['query_1'][1]]);
        } else {
            $query->where($column, $this->getFilterOperator($filter['operator']), "%{$filter['query_1']}%");
        }
    }

    protected function getFilterOperator(string $operator)
    {
        $operators = [
            'eq' => '=',
            'ne' => '<>',
            'gt' => '>',
            'lt' => '<',
            'gte' => '>=',
            'lte' => '<=',
            'like' => 'LIKE',
            'ilike' => 'ILIKE',
            'not like' => 'NOT LIKE',
            'not ilike' => 'NOT ILIKE',
            'contains' => 'LIKE',
            'not contains' => 'NOT LIKE',
            'starts with' => 'LIKE',
            'not starts with' => 'NOT LIKE',
            'ends with' => 'LIKE',
            'not ends with' => 'NOT LIKE',
        ];

        return $operators[$operator] ?? '=';
    }

    protected function applyOrderBy(Builder $query, array $relations, string $orderField, string $direction)
    {
        $query->select($query->getModel()->getTable() . '.*');

        $joinRelation = function ($query, $relation) {
            $relatedModel = $query->getModel()->$relation()->getRelated();
            $relatedTable = $relatedModel->getTable();
            $foreignKey = $query->getModel()->$relation()->getQualifiedForeignKeyName();
            $ownerKey = $query->getModel()->$relation()->getQualifiedParentKeyName();
            $query->leftJoin($relatedTable, $foreignKey, '=', $ownerKey);

            return $relatedTable;
        };

        $relatedTable = '';
        foreach ($relations as $relation) {
            $relatedTable = $joinRelation($query, $relation);
        }

        $relatedColumn = $relatedTable . '.' . $orderField;
        $query->orderBy($relatedColumn, $direction);
    }
}
