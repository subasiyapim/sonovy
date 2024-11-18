<?php


namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class FilterByUserRoleScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */

    public function apply(Builder $builder, Model $model)
    {
        if (App::runningInConsole()) {
            return;
        }

        $user = auth()->user();

        if ($user && $user->roles) {
            $roles = $user->roles->pluck('code')->toArray();

            if (in_array('admin', $roles)) {
                return $builder;
            }
            $builder->where('created_by', $user->id);
        } else {
            $builder->whereRaw('1 = 0');
        }
    }

}
