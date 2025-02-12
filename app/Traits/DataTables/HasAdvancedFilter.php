<?php

namespace App\Traits\DataTables;

use Illuminate\Validation\ValidationException;

trait HasAdvancedFilter
{
    public function scopeAdvancedFilter($query)
    {
        return $this->processQuery($query, [
            'order_column' => request('sort', 'id'),
            'order_direction' => request('order', 'desc'),
            'limit' => request('limit', 25),
            's' => request('s', null),
            'f' => request('f', []),

        ])
            ->paginate(request('limit', $this->defaultLimit ?? config('project.pageLength')))->appends(request()->all());
    }

    public function processQuery($query, $data)
    {

        if ($data["f"]) {
            foreach ($data["f"] as $key => $f) {
                $data["f"][$key] = $f;  //json_decode( $f, true );
            }
        }

        $data = $this->processGlobalSearch($data);

        $v = validator()->make($data, [
            'order_column' => 'sometimes|required', //|in:'.$this->orderableColumns(),
            'order_direction' => 'sometimes|required|in:asc,desc',
            'limit' => 'sometimes|required|integer|min:1',
            's' => 'sometimes|nullable|string',

            // advanced filter
            'filter_match' => 'sometimes|required|in:and,or',
            'f' => 'sometimes|nullable|array',
            'f.*.column' => 'required|in:' . $this->whiteListColumns(),
            'f.*.operator' => 'required_with:f.*.column|in:' . $this->allowedOperators(),
            'f.*.query_1' => 'required',
            'f.*.query_2' => 'required_if:f.*.operator,between,not_between',
        ]);

        if ($v->fails()) {
            throw new ValidationException($v);
        }

        $data = $v->validated();
        return (new FilterQueryBuilder())->apply($query, $data);
    }

    protected function processGlobalSearch($data)
    {

        //    if ( isset( $data[ 'f' ] ) || !isset( $data[ 's' ] ) ) {
        //      return $data;
        //    }

        if (isset($data['s'])) {
            $data['filter_match'] = 'or';

            $data['f'] = array_map(function ($column) use ($data) {
                return [
                    'column' => $column,
                    'operator' => 'contains',
                    'query_1' => $data['s'],
                ];
            }, $this->filterable);
        }

        return $data;
    }

    protected function whiteListColumns()
    {
        return implode(',', $this->filterable);
    }

    protected function allowedOperators()
    {
        return implode(',', [
            'contains',
            'startWith'
        ]);
    }

    protected function orderableColumns()
    {
        return implode(',', $this->orderable);
    }
}
