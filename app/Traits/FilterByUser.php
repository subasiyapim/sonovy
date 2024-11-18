<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait FilterByUser
{
    protected static function boot()
    {
        parent::boot();
        self::creating(fn($model) => self::updateCreatedBy($model));

        self::addGlobalScope('created_by', function (Builder $builder) {
            if (App::runningInConsole()) {
                return;
            }

            $user = auth()->user();

            if ($user && $user->roles) {
                $roles = $user->roles->pluck('code')->toArray();

                if (in_array('admin', $roles) || in_array('super_admin', $roles)) {
                    return $builder;
                }
                $builder->where('created_by', $user->id);
            } else {
                $builder->whereRaw('1 = 0');
            }
        });


    }

    protected static function updateCreatedBy($model): void
    {
        $model->update(['created_by' => auth()->id()]);
    }

}