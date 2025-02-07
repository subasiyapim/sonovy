<?php

use App\Http\Middleware\SocketTenantDomainMiddleware;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::routes([
    'middleware' => [
        'web',
        SocketTenantDomainMiddleware::class,
    ]
]);

Broadcast::channel('tenant.{tenant_id}.reportProcessed.{user_id}', function ($user, $tenant_id, $user_id) {
    return (tenant()->id == $tenant_id) && $user->id == $user_id;
});


