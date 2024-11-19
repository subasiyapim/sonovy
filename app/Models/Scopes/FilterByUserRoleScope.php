<?php


namespace App\Models\Scopes;

use App\Models\Artist;
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
                return;
            }

            if ($user && $user->roles) {
                $roles = $user->roles->pluck('code')->toArray();

                if (in_array('admin', $roles)) {
                    return;
                }

                if ($builder->getModel() instanceof Artist) {
                    $builder->where(function ($query) use ($user) {
                        $query->where('artists.id', 1)
                            ->orWhere('artists.created_by', $user->id);
                    });
                } else {
                    $builder->where('created_by', $user->id);
                }
            } else {
                $builder->whereRaw('1 = 0');
            }
        }
    }

}
