<?php

namespace App\Traits\DataTables;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FilterQueryBuilder
{
    protected $model;
    protected $table;

    public function apply($query, $data)
    {
        $this->model = $query->getModel();
        $this->table = $this->model->getTable();

        if (isset($data['f'])) {
            $query->where(function ($query) use ($data) {
                foreach ($data['f'] as $filter) {
                    $filter['match'] = $data['filter_match'] ?? 'and';
                    $this->makeFilter($query, $filter);
                }
            });
        }

        //dd($query->toSql());

        $this->makeOrder($query, $data);

        //dd($query->toSql());

        return $query;
    }

    protected function makeFilter($query, $filter)
    {
        // if column has - in it, we should concat columns
        if (str_contains($filter['column'], '-')) {
            $columns = explode('-', $filter['column']);
            if ($this->isNestedColumn($columns[0])) {
                [$relation, $col1] = explode('.', $columns[0]);
                [$relation, $col2] = explode('.', $columns[1]);
                $callable = Str::camel($relation);
                $filter['match'] = 'and';
                $filter['column'] = "{$col1}-{$col2}";
                $query->orWhereHas(Str::camel($callable), function ($q) use ($filter) {
                    $this->{Str::camel($filter['operator'])}($filter, $q);
                });
            } else {
                $filter['column'] = "{$this->table}.{$columns[0]}-{$this->table}.{$columns[1]}";
                $this->{Str::camel($filter['operator'])}($filter, $query);
            }
        } else {
            if ($this->isNestedColumn($filter['column'])) {
                [$relation, $filter['column']] = explode('.', $filter['column']);
                $callable = Str::camel($relation);
                $filter['match'] = 'and';

                $query->orWhereHas(Str::camel($callable), function ($q) use ($filter) {
                    $this->{Str::camel($filter['operator'])}($filter, $q);
                });
            } else {
                $filter['column'] = "{$this->table}.{$filter['column']}";
                $this->{Str::camel($filter['operator'])}($filter, $query);
            }
        }
    }

    protected function isNestedColumn($column)
    {
        return str_contains($column, '.');
    }

    protected function makeOrder($query, $data)
    {
        if ($this->isNestedColumn($data['order_column'])) {
            $is_concat = false;
            if (str_contains($data['order_column'], '-')) {
                $columns = explode('-', $data['order_column']);
                [$relationship, $col1] = explode('.', $columns[0]);
                [$relationship, $col2] = explode('.', $columns[1]);
                $is_concat = true;
            } else {
                [$relationship, $column] = explode('.', $data['order_column']);
            }

            $callable = Str::camel($relationship);
            $belongs = $this->model->{$callable}();
            $relatedModel = $belongs->getModel();
            $relatedTable = $relatedModel->getTable();
            $as = "{$relatedTable}";

            if ($is_concat) {
                $data['order_column'] = DB::raw("CONCAT({$as}.{$col1}, ' ', {$as}.{$col2})");
            }

            if (!$belongs instanceof BelongsTo) {
                return;
            }

            $translationsTable = "{$relationship}_translations";
            $query->leftJoin($as, "{$this->table}.{$relationship}_id", '=', "{$as}.id");

            if (Schema::hasTable($translationsTable)) {
                $query->leftJoin("{$translationsTable}", function ($join) use ($translationsTable, $as, $callable) {
                    $join->on("{$translationsTable}.{$callable}_id", '=', "{$as}.id")
                        ->where("{$translationsTable}.locale", app()->getLocale());
                });

                $data['order_column'] = "{$translationsTable}.{$column}";

                $query->reorder(
                    $data['order_column'],
                    $data['order_direction']
                );
            } else {
                if ($is_concat) {
                    $query->reorder(
                        $data['order_column'],
                        $data['order_direction']
                    );
                } elseif (Schema::hasColumn($relatedTable, $column)) {
                    $data['order_column'] = "{$as}.{$column}";

                    $query->reorder(
                        $data['order_column'],
                        $data['order_direction']
                    );
                }
            }
        } else {
            $column = $data['order_column'];
            if (Schema::hasColumn($this->table, $column)) {
                $query->reorder($column, $data['order_direction']);
            } elseif (str_contains($column, '-')) {
                $columns = explode('-', $column);
                $query->reorder(
                    DB::raw("CONCAT({$columns[0]}, ' ', {$columns[1]})"),
                    $data['order_direction']
                );
            }
        }

        //dd($query->toSql());

    }


    public function contains($filter, $query)
    {

        $baseTableName = strtolower(class_basename($query->getModel()));
        $tableName = $baseTableName . '_translations';

        if (Schema::hasTable($tableName) && in_array(
                $filter['column'],
                $query->getConnection()->getSchemaBuilder()->getColumnListing($tableName)
            )) {
            $query = $query->join(
                $tableName,
                $query->getModel()->getTable() . '.id',
                '=',
                $tableName . '.' . $baseTableName . '_id'
            )
                ->where('locale', app()->getLocale())
                ->where($tableName . '.' . $filter['column'], 'like', '%' . $filter['query_1'] . '%');
        } else {
            if (str_contains($filter['column'], '-')) {
                $columns = explode('-', $filter['column']);
                $query = $query->whereRaw(
                    "CONCAT({$columns[0]}, ' ', {$columns[1]}) LIKE ?",
                    ['%' . $filter['query_1'] . '%']
                );
            } else {
                $query = $query->where($filter['column'], 'like', '%' . $filter['query_1'] . '%', $filter['match']);
            }
        }

        //dd($query->toSql());

        return $query;
    }


    public function startWith($filter, $query)
    {
        return $query->where($filter['column'], 'like', $filter['query_1'] . '%');
    }
}
